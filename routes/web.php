<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::controller(SiteController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/tentang-kami', 'about')->name('about');
    Route::get('/produk', 'products')->name('products.index');
    Route::get('/produk/{category}', 'category')->name('products.category');
    Route::get('/produk/{category}/brosur', 'brochure')->name('products.brochure');
    Route::get('/produk/{category}/{package}', 'package')->name('products.package')->scopeBindings();
    Route::get('/solusi/{segment}', 'segment')->name('segments.show');
    Route::get('/portfolio', 'portfolios')->name('portfolio.index');
    Route::get('/portfolio/{portfolio}', 'portfolio')->name('portfolio.show');
    Route::get('/blog', 'blog')->name('blog.index');
    Route::get('/blog/kategori/{postCategory}', 'blog')->name('blog.category');
    Route::get('/blog/{post}', 'post')->name('blog.show');
    Route::get('/promo', 'promos')->name('promos.index');
    Route::get('/promo/{promo}', 'promo')->name('promos.show');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/galeri', 'galleries')->name('gallery.index');
    Route::get('/galeri/{gallery}', 'gallery')->name('gallery.show');
    Route::get('/kontak', 'contact')->name('contact');
    Route::get('/halaman/{page}', 'page')->name('page.show');
    Route::get('/sitemap.xml', 'sitemap')->name('sitemap');
});
