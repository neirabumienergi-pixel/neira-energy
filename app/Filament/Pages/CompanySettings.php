<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\Fields;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Identitas perusahaan & pengaturan global website.
 * Disimpan di tabel settings per kunci grup (company, contact, social, about, units, stats, seo, tracking).
 */
class CompanySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Identitas Perusahaan';

    protected static ?string $title = 'Identitas Perusahaan';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.settings';

    public const KEYS = ['company', 'contact', 'social', 'about', 'units', 'stats', 'hero', 'calculator', 'seo', 'tracking'];

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->canModule('settings');
    }

    public function mount(): void
    {
        $values = Setting::query()->whereIn('key', self::KEYS)->pluck('value', 'key')->all();
        $this->form->fill($values);
    }

    public function form(Form $form): Form
    {
        $text = fn (string $n, string $l) => Forms\Components\TextInput::make($n)->label($l);

        return $form->statePath('data')->schema([
            Forms\Components\Tabs::make()->persistTabInQueryString()->tabs([
                Forms\Components\Tabs\Tab::make('Umum')->icon('heroicon-o-building-office')->schema([
                    $text('company.name', 'Nama brand')->required(),
                    $text('company.legal_name', 'Nama badan usaha'),
                    $text('company.tagline', 'Tagline'),
                    $text('company.slogan', 'Slogan pendek'),
                    Forms\Components\Textarea::make('company.short_about')->label('Deskripsi singkat (footer)')->rows(3)->columnSpanFull(),
                    Fields::image('company.logo', 'Logo', 'brand'),
                    Fields::image('company.logo_white', 'Logo versi putih (footer gelap)', 'brand'),
                    Fields::image('company.favicon', 'Favicon', 'brand'),
                    Forms\Components\TagsInput::make('company.badges')->label('Badge kredensial (header/hero)')->placeholder('Member of AESI')->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Tabs\Tab::make('Kontak')->icon('heroicon-o-phone')->schema([
                    $text('contact.phone', 'Telepon'),
                    $text('contact.whatsapp', 'No. WhatsApp (format 62xxx)')->helperText('Dipakai untuk semua tombol WhatsApp.'),
                    $text('contact.email', 'Email')->email(),
                    $text('contact.hours', 'Jam operasional'),
                    Forms\Components\Textarea::make('contact.wa_message')->label('Pesan WhatsApp default')->rows(2)->columnSpanFull(),
                    Forms\Components\Repeater::make('contact.addresses')->label('Alamat')->schema([
                        $text('label', 'Label')->placeholder('Kantor / Workshop')->required(),
                        Forms\Components\Textarea::make('address')->label('Alamat')->rows(2)->required(),
                        $text('maps_url', 'Link Google Maps')->url(),
                    ])->columns(3)->columnSpanFull(),
                    Forms\Components\Textarea::make('contact.maps_embed')->label('URL embed Google Maps (src iframe)')->rows(2)->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Tabs\Tab::make('Media Sosial')->icon('heroicon-o-share')->schema([
                    $text('social.instagram', 'Instagram URL')->url(),
                    $text('social.facebook', 'Facebook URL')->url(),
                    $text('social.linkedin', 'LinkedIn URL')->url(),
                    $text('social.youtube', 'YouTube URL')->url(),
                    $text('social.tiktok', 'TikTok URL')->url(),
                    $text('social.energy_mart', 'Link Energy Mart (retail)')->url(),
                ])->columns(2),
                Forms\Components\Tabs\Tab::make('Tentang Kami')->icon('heroicon-o-flag')->schema([
                    $text('about.heading', 'Judul'),
                    Fields::image('about.image', 'Gambar', 'brand'),
                    Forms\Components\RichEditor::make('about.body')->label('Profil perusahaan')->columnSpanFull(),
                    Forms\Components\Textarea::make('about.vision')->label('Visi')->rows(2)->columnSpanFull(),
                    Forms\Components\Repeater::make('about.missions')->label('Misi')->simple(Forms\Components\TextInput::make('text')->required())->columnSpanFull(),
                    Forms\Components\Repeater::make('about.why')->label('Kenapa Neira berbeda?')->schema([
                        $text('icon', 'Ikon heroicon'), $text('title', 'Judul')->required(), Forms\Components\Textarea::make('text')->label('Teks')->rows(2),
                    ])->columns(3)->columnSpanFull(),
                    Forms\Components\Repeater::make('about.esg')->label('Komitmen ESG')->schema([
                        $text('title', 'Judul')->required(), Forms\Components\Textarea::make('text')->label('Teks')->rows(2),
                    ])->columns(2)->columnSpanFull(),
                    Fields::image('about.team_image', 'Foto tim', 'brand'),
                ])->columns(2),
                Forms\Components\Tabs\Tab::make('Unit Bisnis')->icon('heroicon-o-rectangle-group')->schema([
                    Forms\Components\Repeater::make('units')->hiddenLabel()->schema([
                        $text('name', 'Nama unit')->required(),
                        $text('label', 'Label')->placeholder('EPC & Engineering'),
                        Fields::image('logo', 'Logo', 'units'),
                        $text('url', 'Link'),
                        Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                        Forms\Components\TagsInput::make('services')->label('Layanan utama')->columnSpanFull(),
                    ])->columns(2)->collapsible()->itemLabel(fn (array $state) => $state['name'] ?? null),
                ]),
                Forms\Components\Tabs\Tab::make('Angka')->icon('heroicon-o-chart-bar')->schema([
                    Forms\Components\Repeater::make('stats')->hiddenLabel()->schema([
                        $text('value', 'Nilai')->required()->placeholder('10+'),
                        $text('unit', 'Satuan')->placeholder('MWp'),
                        $text('label', 'Keterangan')->required(),
                    ])->columns(3)->grid(2),
                ]),
                Forms\Components\Tabs\Tab::make('Tampilan Hero')->icon('heroicon-o-film')->schema([
                    Forms\Components\Toggle::make('hero.motion')->label('Motion graphic (sinar matahari, aliran energi, partikel)')->default(true)
                        ->helperText('Animasi ringan di atas gambar/video latar. Otomatis nonaktif bila pengunjung memilih "kurangi animasi".'),
                    Forms\Components\Toggle::make('hero.video_on_mobile')->label('Putar video di HP')->default(false)
                        ->helperText('Mati = HP menampilkan gambar poster saja (hemat kuota pengunjung).'),
                    Forms\Components\Select::make('hero.overlay')->label('Kegelapan lapisan warna')->options(['light' => 'Ringan', 'medium' => 'Sedang', 'dark' => 'Gelap'])->default('medium')
                        ->helperText('Lapisan hijau di atas latar agar teks tetap terbaca.'),
                ])->columns(1),
                Forms\Components\Tabs\Tab::make('Kalkulator')->icon('heroicon-o-calculator')->schema([
                    Forms\Components\Placeholder::make('calc_help')->hiddenLabel()->columnSpanFull()
                        ->content('Asumsi untuk kalkulator hemat & simulasi surya di beranda. Rekomendasi diambil dari paket di kategori terpilih: paket dengan rentang tagihan yang cocok, atau paket dengan kapasitas terdekat. Kebutuhan > 1,5x paket terbesar dianggap proyek custom (pakai harga per kWp).'),
                    Forms\Components\Select::make('calculator.category_id')->label('Kategori paket untuk rekomendasi')
                        ->options(fn () => \App\Filament\Resources\ProductPackageResource::categoryOptions())->placeholder('Otomatis: PLTS On Grid'),
                    $text('calculator.price_per_kwp', 'Harga per kWp (jika tidak ada paket cocok)')->numeric()->prefix('Rp'),
                    $text('calculator.tariff_residential', 'Tarif listrik rumah')->numeric()->prefix('Rp')->suffix('/kWh'),
                    $text('calculator.tariff_business', 'Tarif listrik bisnis / instansi')->numeric()->prefix('Rp')->suffix('/kWh'),
                    $text('calculator.offset_residential', 'Porsi kebutuhan ditanggung PLTS – rumah')->numeric()->step(0.05)->helperText('0–1. Rumah banyak memakai listrik malam, mis. 0,6'),
                    $text('calculator.offset_business', 'Porsi kebutuhan ditanggung PLTS – bisnis')->numeric()->step(0.05)->helperText('0–1, mis. 0,8'),
                    $text('calculator.psh', 'Peak sun hours')->numeric()->step(0.1)->suffix('jam/hari'),
                    $text('calculator.performance_ratio', 'Performance ratio')->numeric()->step(0.01),
                    $text('calculator.emission_factor', 'Faktor emisi grid')->numeric()->step(0.01)->suffix('kg CO₂/kWh'),
                    $text('calculator.min_bill', 'Slider tagihan – minimum')->numeric()->prefix('Rp'),
                    $text('calculator.max_bill', 'Slider tagihan – maksimum')->numeric()->prefix('Rp'),
                ])->columns(2),
                Forms\Components\Tabs\Tab::make('SEO & Tracking')->icon('heroicon-o-magnifying-glass')->schema([
                    $text('seo.meta_title', 'Meta title default')->columnSpanFull(),
                    Forms\Components\Textarea::make('seo.meta_description')->label('Meta description default')->rows(2)->columnSpanFull(),
                    Fields::image('seo.og_image', 'Gambar share default', 'seo'),
                    $text('seo.keywords', 'Kata kunci'),
                    $text('tracking.ga4', 'Google Analytics 4 ID')->placeholder('G-XXXXXXX'),
                    $text('tracking.gtm', 'Google Tag Manager ID')->placeholder('GTM-XXXXXX'),
                    $text('tracking.meta_pixel', 'Meta Pixel ID'),
                ])->columns(2),
            ])->columnSpanFull(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('save')->label('Simpan')->action('save')->keyBindings(['mod+s'])];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        foreach (self::KEYS as $key) {
            if (array_key_exists($key, $state)) {
                Setting::put($key, $state[$key]);
            }
        }
        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }
}
