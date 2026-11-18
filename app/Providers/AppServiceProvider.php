<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\ProductCategory;
use App\Models\Promo;
use App\Models\Segment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.site', function ($view) {
            static $nav;
            $nav ??= Schema::hasTable('product_categories') ? [
                'navCategories' => ProductCategory::active()->whereNull('parent_id')->ordered()
                    ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort')])->get(),
                'navSegments' => Segment::active()->ordered()->get(),
                'navPages' => Page::active()->where('show_in_menu', true)->ordered()->get(),
                'hasPromo' => Promo::running()->where('show_on_site', true)->exists(),
            ] : ['navCategories' => collect(), 'navSegments' => collect(), 'navPages' => collect(), 'hasPromo' => false];
            $view->with($nav);
        });
    }
}
