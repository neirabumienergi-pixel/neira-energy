<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasSlug, Sortable;

    protected $guarded = [];

    protected $casts = ['examples' => 'array', 'seo' => 'array', 'is_active' => 'boolean'];

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }
}
