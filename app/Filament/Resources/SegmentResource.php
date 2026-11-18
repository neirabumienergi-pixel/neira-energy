<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\SegmentResource\Pages;
use App\Models\Segment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SegmentResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'products';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Segment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Produk & Layanan';

    protected static ?string $modelLabel = 'Segmen';

    protected static ?string $pluralModelLabel = 'Segmen Pasar';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Fields::title('name', 'Nama segmen'),
                Fields::slug(),
                Forms\Components\TextInput::make('tagline'),
                Forms\Components\TextInput::make('icon')->label('Ikon (heroicon)')->placeholder('heroicon-o-building-office')
                    ->helperText('Nama ikon dari heroicons.com, mis. heroicon-o-home'),
                Forms\Components\Textarea::make('short_description')->label('Ringkasan')->rows(3)->columnSpanFull(),
                Forms\Components\TagsInput::make('examples')->label('Contoh klien (NGO, Desa, Sekolah, ...)')->columnSpanFull(),
                Fields::image('image', 'Gambar', 'segments'),
                Forms\Components\RichEditor::make('content')->label('Konten landing page')->columnSpanFull(),
                Fields::sort(),
                Fields::active(),
            ])->columns(2),
            Forms\Components\Section::make('Tampilan di hero beranda')
                ->description('Muncul saat pengunjung memilih segmen ini di "Saya adalah…" pada beranda.')
                ->schema([
                    Forms\Components\Toggle::make('show_in_hero')->label('Tampilkan di hero')->default(true),
                    Forms\Components\Select::make('calc_profile')->label('Profil kalkulator')->options(['residential' => 'Rumah (input tagihan, tarif & pola rumah tangga)', 'business' => 'Bisnis / Instansi (input tagihan)', 'project' => 'Proyek (input kapasitas kWp → estimasi investasi)'])->default('business'),
                    Forms\Components\TextInput::make('hero_title')->label('Judul hero')->placeholder('Kosong = "Solusi energi surya untuk {nama}"')->columnSpanFull(),
                    Forms\Components\Textarea::make('hero_text')->label('Teks hero')->rows(2)->placeholder('Kosong = ringkasan segmen')->columnSpanFull(),
                    Forms\Components\TagsInput::make('hero_points')->label('Poin keunggulan (maks. 4 disarankan)')->columnSpanFull(),
                    Fields::image('hero_image', 'Gambar latar hero', 'segments'),
                    Fields::video('hero_video', 'Video latar hero (opsional)', 'segments'),
                    Forms\Components\Grid::make(1)->schema([
                        Forms\Components\TextInput::make('hero_cta_label')->label('Tombol – teks')->placeholder('Konsultasi gratis'),
                        Forms\Components\TextInput::make('hero_cta_url')->label('Tombol – link')->placeholder('Kosong = WhatsApp dengan hasil kalkulator'),
                    ])->columnSpan(1),
                ])->columns(2)->collapsible(),
            Fields::seo(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Segmen')->searchable(),
                Tables\Columns\TextColumn::make('tagline')->limit(50),
                Tables\Columns\TextColumn::make('portfolios_count')->counts('portfolios')->label('Portfolio'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSegments::route('/'),
            'create' => Pages\CreateSegment::route('/create'),
            'edit' => Pages\EditSegment::route('/{record}/edit'),
        ];
    }
}
