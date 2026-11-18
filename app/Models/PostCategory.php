<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    use HasSlug;

    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
