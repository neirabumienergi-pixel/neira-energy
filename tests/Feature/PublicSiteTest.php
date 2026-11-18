<?php

namespace Tests\Feature;

use App\Models\ProductPackage;
use App\Models\Setting;
use Database\Seeders\NeiraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->withoutVite();
        $this->seed(NeiraSeeder::class);
    }

    public function test_all_public_pages_render(): void
    {
        $pkg = ProductPackage::with('category')->first();
        foreach (['/', '/tentang-kami', '/produk', '/produk/plts', '/produk/plts/brosur', '/produk/pju-tenaga-surya', $pkg->url(),
            '/solusi/hunian', '/portfolio', '/blog', '/promo', '/faq', '/galeri', '/kontak', '/halaman/perizinan-plts', '/sitemap.xml'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_home_hero_contains_segments_and_calculator_data(): void
    {
        \App\Models\Segment::where('name', 'Komunitas')->update(['hero_title' => 'Judul hero komunitas uji', 'show_in_hero' => true]);
        \App\Models\Segment::where('name', 'Kemitraan EPC')->update(['show_in_hero' => false]);

        $this->get('/')->assertOk()
            ->assertSee('solarHero', false)
            ->assertSee('Judul hero komunitas uji')
            ->assertSee('PLTS On Grid 10 kWp')
            ->assertDontSee('Partner andal untuk developer');
    }

    public function test_home_has_single_h1_and_no_kicker_labels(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertStringNotContainsString('class="eyebrow"', $html);
        $this->assertStringContainsString('id="kalkulator"', $html);
        $this->assertStringContainsString('project', $html);
        $this->assertSame(3, \App\Models\Segment::where('calc_profile', 'project')->count());
    }

    public function test_hero_video_and_motion_settings(): void
    {
        \App\Models\Banner::query()->first()->update(['video_url' => 'https://cdn.example.com/hero.mp4']);
        Setting::put('hero', ['motion' => true, 'video_on_mobile' => false, 'overlay' => 'dark']);

        $this->get('/')->assertOk()
            ->assertSee('cdn.example.com', false)->assertSee('hero.mp4', false)
            ->assertSee('hero-motion', false)
            ->assertSee('to-brand-950/75', false);

        Setting::put('hero', ['motion' => false]);
        $this->get('/')->assertDontSee('hero-motion', false);
    }

    public function test_package_page_shows_admin_managed_data(): void
    {
        $pkg = ProductPackage::with('category')->where('name', 'PLTS On Grid 5 kWp')->first();
        $pkg->update(['base_price' => 61_234_000]);

        $this->get($pkg->url())->assertOk()->assertSee('PLTS On Grid 5 kWp')->assertSee('Rp 61.234.000')->assertSee('Monocrystalline');
    }

    public function test_brochure_lists_packages_of_child_categories(): void
    {
        $this->get('/produk/plts/brosur')->assertOk()
            ->assertSee('PLTS On Grid 3 kWp')->assertSee('PLTS Off Grid 1 kWp')->assertSee('PLTS Hybrid 10 kWp');
    }

    public function test_inactive_package_returns_404(): void
    {
        $pkg = ProductPackage::with('category')->first();
        $url = $pkg->url();
        $pkg->update(['is_active' => false]);

        $this->get($url)->assertNotFound();
    }

    public function test_package_url_must_match_its_category(): void
    {
        $pkg = ProductPackage::where('name', 'PLTS On Grid 3 kWp')->first();
        $this->get('/produk/plts-hybrid/'.$pkg->slug)->assertNotFound();
    }

    public function test_settings_changes_are_reflected_immediately(): void
    {
        $contact = Setting::get('contact');
        $contact['phone'] = '021 999 888';
        Setting::put('contact', $contact);

        $this->get('/kontak')->assertSee('021 999 888');
    }
}
