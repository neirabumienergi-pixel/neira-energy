<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasSlug, Sortable;

    protected $guarded = [];

    protected $casts = ['brochure' => 'array', 'seo' => 'array', 'has_packages' => 'boolean', 'is_active' => 'boolean'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    public function packages()
    {
        return $this->hasMany(ProductPackage::class)->orderBy('sort')->orderBy('capacity');
    }

    public function promos()
    {
        return $this->belongsToMany(Promo::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    /** Semua id kategori ini + turunannya. */
    public function descendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }

    public function ancestorIds(): array
    {
        $ids = [];
        $p = $this->parent;
        while ($p) {
            $ids[] = $p->id;
            $p = $p->parent;
        }

        return $ids;
    }

    public function getFullNameAttribute(): string
    {
        return $this->parent ? $this->parent->name.' › '.$this->name : $this->name;
    }

    public function url(): string
    {
        return $this->external_url ?: route('products.category', $this);
    }
}
