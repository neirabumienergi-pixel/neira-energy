<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasSlug;

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime', 'ends_at' => 'datetime', 'discount_value' => 'float', 'max_discount' => 'float',
        'show_on_site' => 'boolean', 'is_active' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function packages()
    {
        return $this->belongsToMany(ProductPackage::class);
    }

    public function scopeRunning(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function isRunning(): bool
    {
        return $this->is_active
            && (! $this->starts_at || $this->starts_at->isPast())
            && (! $this->ends_at || $this->ends_at->isFuture());
    }

    public function appliesToPackage(ProductPackage $package): bool
    {
        return match ($this->applies_to) {
            'all' => true,
            'packages' => $this->packages->contains('id', $package->id),
            'categories' => $this->categories->pluck('id')
                ->intersect(array_merge([$package->product_category_id], $package->category?->ancestorIds() ?? []))
                ->isNotEmpty(),
            default => false,
        };
    }

    public function discountFor(float $price): float
    {
        $d = $this->discount_type === 'percent' ? $price * $this->discount_value / 100 : $this->discount_value;
        if ($this->max_discount) {
            $d = min($d, $this->max_discount);
        }

        return round(min($d, $price), 2);
    }

    public function getLabelAttribute(): string
    {
        return $this->discount_type === 'percent'
            ? rtrim(rtrim(number_format($this->discount_value, 2, ',', '.'), '0'), ',').'%'
            : 'Rp '.number_format($this->discount_value, 0, ',', '.');
    }
}
