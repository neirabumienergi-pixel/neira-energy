<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Certification;
use App\Models\Client;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\ProductCategory;
use App\Models\ProductPackage;
use App\Models\Promo;
use App\Models\Segment;
use App\Models\Testimonial;
use App\Services\SolarCalculator;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home(SolarCalculator $calculator)
    {
        $banners = Banner::live()->ordered()->get();
        $heroSegments = Segment::active()->where('show_in_hero', true)->ordered()->get();

        return view('site.home', [
            'banners' => $banners,
            'hero' => [
                'banners' => $banners->map(fn ($b) => [
                    'image' => media($b->image), 'image_mobile' => media($b->image_mobile),
                    'video' => $b->video ? media($b->video) : ($b->video_url ?: null),
                ])->values(),
                'display' => [
                    'motion' => (bool) (setting('hero.motion') ?? true),
                    'video_on_mobile' => (bool) setting('hero.video_on_mobile', false),
                ],
                'segments' => $heroSegments->map(fn (Segment $s) => [
                    'slug' => $s->slug, 'name' => $s->name, 'tagline' => $s->tagline,
                    'icon' => $this->svg($s->icon, 'heroicon-o-bolt'),
                    'title' => $s->hero_title ?: 'Solusi energi surya untuk '.$s->name,
                    'text' => $s->hero_text ?: $s->short_description,
                    'points' => array_values(array_filter($s->hero_points ?? [])),
                    'image' => media($s->hero_image ?: $s->image),
                    'video' => $s->hero_video ? media($s->hero_video) : null,
                    'cta_label' => $s->hero_cta_label ?: 'Konsultasi gratis',
                    'cta_url' => $s->hero_cta_url,
                    'profile' => $s->calc_profile ?: 'business',
                    'url' => route('segments.show', $s),
                ])->values(),
                'calc' => $calculator->frontendPayload(),
                'waNumber' => preg_replace('/\D/', '', (string) setting('contact.whatsapp')),
            ],
            'categories' => ProductCategory::active()->whereNull('parent_id')->ordered()->with(['children' => fn ($q) => $q->where('is_active', true)])->get(),
            'segments' => Segment::active()->ordered()->get(),
            'featuredPackages' => ProductPackage::active()->where('is_featured', true)->with('category')->ordered()->take(6)->get(),
            'portfolios' => Portfolio::active()->where('is_featured', true)->ordered()->take(6)->get(),
            'clients' => Client::active()->ordered()->get(),
            'testimonials' => Testimonial::active()->ordered()->take(6)->get(),
            'certifications' => Certification::active()->ordered()->get(),
            'posts' => Post::published()->latest('published_at')->take(3)->get(),
            'promos' => Promo::running()->where('show_on_site', true)->latest()->take(3)->get(),
        ]);
    }

    public function about()
    {
        return view('site.about', [
            'certifications' => Certification::active()->ordered()->get(),
            'clients' => Client::active()->ordered()->get(),
        ]);
    }

    public function products()
    {
        return view('site.products', [
            'categories' => ProductCategory::active()->whereNull('parent_id')->ordered()
                ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort')])->get(),
        ]);
    }

    public function category(ProductCategory $category)
    {
        abort_unless($category->is_active, 404);
        if ($category->external_url) {
            return redirect()->away($category->external_url);
        }

        return view('site.category', $this->categoryData($category));
    }

    public function brochure(ProductCategory $category)
    {
        abort_unless($category->is_active, 404);

        return view('site.brochure', $this->categoryData($category));
    }

    protected function categoryData(ProductCategory $category): array
    {
        $category->load(['parent', 'children' => fn ($q) => $q->where('is_active', true)->orderBy('sort')]);
        $ids = $category->descendantIds();

        return [
            'category' => $category,
            'packages' => ProductPackage::active()->whereIn('product_category_id', $ids)->with('category')->get()
                ->sortBy(fn ($p) => sprintf('%05d-%05d-%015.3f', $p->category->sort, $p->product_category_id, $p->capacity))->values(),
            'portfolios' => Portfolio::active()->whereIn('product_category_id', $ids)->ordered()->take(6)->get(),
        ];
    }

    public function package(ProductCategory $category, ProductPackage $package)
    {
        abort_unless($package->is_active, 404);
        $promo = $package->bestPromo();

        return view('site.package', [
            'category' => $category->load('parent'),
            'package' => $package,
            'promo' => $promo,
            'finalPrice' => $package->finalPrice($promo),
            'siblings' => ProductPackage::active()->where('product_category_id', $package->product_category_id)
                ->whereKeyNot($package->id)->orderBy('capacity')->take(4)->get(),
        ]);
    }

    public function segment(Segment $segment)
    {
        abort_unless($segment->is_active, 404);

        return view('site.segment', [
            'segment' => $segment,
            'portfolios' => $segment->portfolios()->where('is_active', true)->ordered()->take(6)->get(),
            'categories' => ProductCategory::active()->whereNull('parent_id')->ordered()->get(),
            'segments' => Segment::active()->ordered()->get(),
        ]);
    }

    public function portfolios(Request $request)
    {
        $query = Portfolio::active()->with('segment')->ordered()
            ->when($request->query('tipe'), fn ($q, $v) => $q->where('system_type', $v))
            ->when($request->query('segmen'), fn ($q, $v) => $q->whereHas('segment', fn ($s) => $s->where('slug', $v)));

        return view('site.portfolios', [
            'portfolios' => $query->paginate(12)->withQueryString(),
            'types' => Portfolio::active()->whereNotNull('system_type')->distinct()->pluck('system_type'),
            'segments' => Segment::active()->ordered()->get(),
            'mapPoints' => Portfolio::active()->whereNotNull('lat')->get(['title', 'slug', 'lat', 'lng', 'capacity', 'capacity_unit']),
        ]);
    }

    public function portfolio(Portfolio $portfolio)
    {
        abort_unless($portfolio->is_active, 404);

        return view('site.portfolio', [
            'portfolio' => $portfolio->load('segment', 'category'),
            'related' => Portfolio::active()->whereKeyNot($portfolio->id)->ordered()->take(3)->get(),
        ]);
    }

    public function blog(Request $request, ?PostCategory $postCategory = null)
    {
        $posts = Post::published()->with('category')->latest('published_at')
            ->when($postCategory, fn ($q) => $q->where('post_category_id', $postCategory->id))
            ->when($request->query('q'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->paginate(9)->withQueryString();

        return view('site.blog', [
            'posts' => $posts,
            'activeCategory' => $postCategory,
            'categories' => PostCategory::whereHas('posts', fn ($q) => $q->published())->get(),
        ]);
    }

    public function post(Post $post)
    {
        abort_unless($post->status === 'published' && $post->published_at?->isPast(), 404);
        $post->increment('views');

        return view('site.post', [
            'post' => $post->load('category', 'author'),
            'related' => Post::published()->whereKeyNot($post->id)
                ->when($post->post_category_id, fn ($q) => $q->where('post_category_id', $post->post_category_id))
                ->latest('published_at')->take(3)->get(),
        ]);
    }

    public function promos()
    {
        return view('site.promos', ['promos' => Promo::running()->where('show_on_site', true)->latest()->get()]);
    }

    public function promo(Promo $promo)
    {
        abort_unless($promo->show_on_site, 404);
        $promo->load('packages.category', 'categories');
        $packages = ProductPackage::active()->with('category.parent')->whereNotNull('base_price')->get()
            ->filter(fn ($p) => $promo->appliesToPackage($p))->take(12);

        return view('site.promo', ['promo' => $promo, 'packages' => $packages]);
    }

    public function faq()
    {
        return view('site.faq', ['groups' => Faq::active()->ordered()->get()->groupBy('group')]);
    }

    public function galleries()
    {
        return view('site.galleries', ['galleries' => Gallery::active()->ordered()->get()]);
    }

    public function gallery(Gallery $gallery)
    {
        abort_unless($gallery->is_active, 404);

        return view('site.gallery', ['gallery' => $gallery]);
    }

    public function contact()
    {
        return view('site.contact', ['segments' => Segment::active()->ordered()->get()]);
    }

    public function page(Page $page)
    {
        abort_unless($page->is_active, 404);

        return view('site.page', ['page' => $page]);
    }

    public function sitemap()
    {
        $urls = collect([route('home'), route('about'), route('products.index'), route('portfolio.index'), route('blog.index'),
            route('promos.index'), route('faq'), route('gallery.index'), route('contact')]);
        ProductCategory::active()->whereNull('external_url')->get()->each(fn ($c) => $urls->push(route('products.category', $c)));
        ProductPackage::active()->with('category')->get()->each(fn ($p) => $urls->push($p->url()));
        Segment::active()->get()->each(fn ($s) => $urls->push(route('segments.show', $s)));
        Portfolio::active()->get()->each(fn ($p) => $urls->push(route('portfolio.show', $p)));
        Post::published()->get()->each(fn ($p) => $urls->push(route('blog.show', $p)));
        Page::active()->get()->each(fn ($p) => $urls->push(route('page.show', $p)));

        return response()->view('site.sitemap', ['urls' => $urls], 200)->header('Content-Type', 'application/xml');
    }

    protected function svg(?string $name, string $fallback): string
    {
        foreach (array_filter([$name, $fallback]) as $n) {
            try {
                return svg($n, 'h-4 w-4')->toHtml();
            } catch (\Throwable) {
            }
        }

        return '';
    }
}
