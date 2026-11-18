<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class ProductPackage extends Model
{
    use HasSlug, Sortable;

    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array', 'specifications' => 'array', 'components' => 'array', 'scope_of_work' => 'array', 'seo' => 'array',
        'capacity' => 'float', 'base_price' => 'float', 'strike_price' => 'float', 'est_production_kwh_month' => 'float',
        'battery_kwh' => 'float', 'area_required_m2' => 'float', 'min_monthly_bill' => 'float', 'max_monthly_bill' => 'float',
        'is_featured' => 'boolean', 'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function promos()
    {
        return $this->belongsToMany(Promo::class);
    }

    /** Promo aktif dengan potongan terbesar untuk paket ini. */
    public function bestPromo(): ?Promo
    {
        if (! $this->base_price) {
            return null;
        }

        return Promo::running()->get()
            ->filter(fn (Promo $p) => $p->appliesToPackage($this))
            ->sortByDesc(fn (Promo $p) => $p->discountFor($this->base_price))
            ->first();
    }

    public function finalPrice(?Promo $promo = null): ?float
    {
        if ($this->base_price === null) {
            return null;
        }
        $promo ??= $this->bestPromo();

        return max(0, $this->base_price - ($promo?->discountFor($this->base_price) ?? 0));
    }

    public function getCapacityLabelAttribute(): string
    {
        if ($this->capacity === null) {
            return '';
        }
        $n = rtrim(rtrim(number_format($this->capacity, 3, ',', '.'), '0'), ',');

        return $n.' '.$this->capacity_unit;
    }

    public function url(): string
    {
        return route('products.package', [$this->category, $this]);
    }
}
