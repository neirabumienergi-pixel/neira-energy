<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasSlug, Sortable;

    protected $guarded = [];

    protected $casts = ['examples' => 'array', 'hero_points' => 'array', 'seo' => 'array', 'is_active' => 'boolean', 'show_in_hero' => 'boolean'];

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }
}
