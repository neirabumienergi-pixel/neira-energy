<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\ProductPackage;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function package(float $price = 100_000_000): ProductPackage
    {
        $parent = ProductCategory::create(['name' => 'PLTS']);
        $child = ProductCategory::create(['name' => 'On Grid', 'parent_id' => $parent->id, 'has_packages' => true]);

        return ProductPackage::create(['product_category_id' => $child->id, 'name' => 'Paket 10 kWp', 'capacity' => 10, 'base_price' => $price]);
    }

    public function test_percent_discount_respects_max_discount(): void
    {
        $pkg = $this->package();
        Promo::create(['title' => 'Promo 10%', 'discount_type' => 'percent', 'discount_value' => 10, 'max_discount' => 5_000_000]);

        $this->assertSame(95_000_000.0, $pkg->finalPrice());
    }

    public function test_best_promo_is_chosen_and_inactive_or_expired_promos_ignored(): void
    {
        $pkg = $this->package();
        Promo::create(['title' => 'Fixed 3jt', 'discount_type' => 'fixed', 'discount_value' => 3_000_000]);
        Promo::create(['title' => 'Nonaktif', 'discount_type' => 'fixed', 'discount_value' => 50_000_000, 'is_active' => false]);
        Promo::create(['title' => 'Kedaluwarsa', 'discount_type' => 'fixed', 'discount_value' => 40_000_000, 'ends_at' => now()->subDay()]);
        Promo::create(['title' => 'Belum mulai', 'discount_type' => 'fixed', 'discount_value' => 30_000_000, 'starts_at' => now()->addDay()]);

        $this->assertSame('Fixed 3jt', $pkg->bestPromo()->title);
        $this->assertSame(97_000_000.0, $pkg->finalPrice());
    }

    public function test_category_promo_applies_to_packages_in_child_categories(): void
    {
        $pkg = $this->package();
        $other = ProductCategory::create(['name' => 'PJU']);
        $promoParent = Promo::create(['title' => 'Promo PLTS', 'discount_type' => 'percent', 'discount_value' => 5, 'applies_to' => 'categories']);
        $promoParent->categories()->attach($pkg->category->parent_id);
        $promoOther = Promo::create(['title' => 'Promo PJU', 'discount_type' => 'percent', 'discount_value' => 50, 'applies_to' => 'categories']);
        $promoOther->categories()->attach($other->id);

        $this->assertSame('Promo PLTS', $pkg->bestPromo()->title);
        $this->assertSame(95_000_000.0, $pkg->finalPrice());
    }

    public function test_package_specific_promo_only_applies_to_selected_packages(): void
    {
        $pkg = $this->package();
        $other = ProductPackage::create(['product_category_id' => $pkg->product_category_id, 'name' => 'Paket 20 kWp', 'capacity' => 20, 'base_price' => 200_000_000]);
        $promo = Promo::create(['title' => 'Khusus 10 kWp', 'discount_type' => 'fixed', 'discount_value' => 1_000_000, 'applies_to' => 'packages']);
        $promo->packages()->attach($pkg->id);

        $this->assertSame(99_000_000.0, $pkg->finalPrice());
        $this->assertNull($other->bestPromo());
        $this->assertSame(200_000_000.0, $other->finalPrice());
    }

    public function test_discount_never_exceeds_price_and_package_without_price_has_no_final_price(): void
    {
        $pkg = $this->package(1_000_000);
        Promo::create(['title' => 'Besar', 'discount_type' => 'fixed', 'discount_value' => 5_000_000]);
        $this->assertSame(0.0, $pkg->finalPrice());

        $pkg->update(['base_price' => null]);
        $this->assertNull($pkg->fresh()->finalPrice());
    }
}
