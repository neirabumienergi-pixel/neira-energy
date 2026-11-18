<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            if (blank($model->slug)) {
                $base = Str::slug($model->{$model->slugSource()});
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->when($model->exists, fn ($q) => $q->whereKeyNot($model->getKey()))->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $model->slug = $slug;
            }
        });
    }

    protected function slugSource(): string
    {
        return property_exists($this, 'slugFrom') ? $this->slugFrom : (isset($this->attributes['title']) ? 'title' : 'name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
