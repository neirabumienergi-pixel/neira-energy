<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasSlug, Sortable;

    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'items' => 'array', 'blocks' => 'array', 'seo' => 'array', 'event_date' => 'date'];
}
