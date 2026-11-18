<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasSlug, Sortable;

    protected $guarded = [];

    protected $casts = ['gallery' => 'array', 'seo' => 'array', 'capacity' => 'float', 'is_featured' => 'boolean', 'is_active' => 'boolean'];

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function getCapacityLabelAttribute(): string
    {
        return $this->capacity ? rtrim(rtrim(number_format($this->capacity, 2, ',', '.'), '0'), ',').' '.$this->capacity_unit : '';
    }
}
