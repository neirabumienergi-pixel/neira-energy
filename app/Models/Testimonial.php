<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use Sortable;

    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];
}
