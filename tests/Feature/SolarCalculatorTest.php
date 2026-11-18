<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\ProductPackage;
use App\Models\Setting;
use App\Services\SolarCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolarCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $cat = ProductCategory::create(['name' => 'PLTS On Grid', 'has_packages' => true]);
        foreach ([3 => 42e6, 5 => 65e6, 10 => 120e6, 20 => 225e6] as $kwp => $price) {
            ProductPackage::create(['product_category_id' => $cat->id, 'name' => "On Grid {$kwp} kWp", 'capacity' => $kwp, 'base_price' => $price]);
        }
    }

    protected function calc(): SolarCalculator
    {
        return new SolarCalculator(['tariff_residential' => 1500, 'tariff_business' => 1500, 'psh' => 4, 'performance_ratio' => 0.8,
            'offset_residential' => 0.6, 'offset_business' => 0.8, 'price_per_kwp' => 10_000_000, 'emission_factor' => 0.8]);
    }

    public function test_recommends_nearest_package_and_caps_saving_to_self_consumption(): void
    {
        // 3jt / 1500 = 2000 kWh; x0.6 = 1200 kWh; / (4*0.8*30=96) = 12.5 kWp → paket terdekat 10 kWp
        $r = $this->calc()->estimate(3_000_000, 'residential');

        $this->assertSame('On Grid 10 kWp', $r['package']->name);
        $this->assertSame(10.0, $r['kwp']);
        $this->assertEquals(960, $r['production_month']);
        $this->assertEquals(960 * 1500, $r['saving_month']);       // semua produksi terpakai sendiri
        $this->assertEquals(120_000_000, $r['cost']);
        $this->assertSame(round(120e6 / (960 * 1500 * 12), 1), $r['payback_years']);
    }

    public function test_oversized_package_does_not_count_wasted_export(): void
    {
        // 1jt rumah: 666 kWh, x0.6 = 400 kWh → 4,2 kWp → terdekat 5 kWp (produksi 480 kWh > 400 kWh dipakai)
        $r = $this->calc()->estimate(1_000_000, 'residential');
        $this->assertSame(5.0, $r['kwp']);
        $this->assertEquals(round(1_000_000 / 1500 * 0.6 * 1500), $r['saving_month']);
    }

    public function test_large_demand_falls_back_to_custom_estimate(): void
    {
        $r = $this->calc()->estimate(100_000_000, 'business'); // ±555 kWp
        $this->assertNull($r['package']);
        $this->assertEquals($r['kwp'] * 10_000_000, $r['cost']);
    }

    public function test_admin_bill_range_overrides_capacity_match(): void
    {
        ProductPackage::where('name', 'On Grid 20 kWp')->update(['min_monthly_bill' => 2_500_000, 'max_monthly_bill' => 3_500_000]);
        $this->assertSame('On Grid 20 kWp', $this->calc()->estimate(3_000_000, 'residential')['package']->name);
    }

    public function test_reads_settings_and_ignores_blank_values(): void
    {
        Setting::put('calculator', ['psh' => 5, 'tariff_business' => '', 'price_per_kwp' => null]);
        $c = new SolarCalculator;
        $this->assertSame(5.0, $c->config['psh']);
        $this->assertSame(SolarCalculator::DEFAULTS['tariff_business'], $c->config['tariff_business']);
    }
}
