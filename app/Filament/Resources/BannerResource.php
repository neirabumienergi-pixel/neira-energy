<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Banner Halaman Utama';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Konten')->schema([
                Forms\Components\TextInput::make('title')->label('Judul besar')->required(),
                Forms\Components\TextInput::make('subtitle')->label('Subjudul / label kecil'),
                Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                Fields::image('image', 'Gambar desktop (rasio 16:9, min 1600px)', 'banners')->required(),
                Fields::image('image_mobile', 'Gambar mobile (opsional, potret)', 'banners'),
            ])->columns(2),
            Forms\Components\Section::make('Video latar (opsional)')
                ->description('Jika diisi, video diputar otomatis (tanpa suara, berulang) menggantikan gambar desktop.')
                ->schema([
                    Fields::video('video', 'Upload video', 'banners'),
                    Forms\Components\TextInput::make('video_url')->label('…atau link file video (.mp4/.webm)')->url()
                        ->placeholder('https://cdn.example.com/hero.mp4')->helperText('Link langsung ke file video (bukan YouTube).'),
                ])->columns(2)->collapsible(),
            Forms\Components\Section::make('Tombol')->schema([
                Forms\Components\TextInput::make('cta_label')->label('Tombol utama – teks'),
                Forms\Components\TextInput::make('cta_url')->label('Tombol utama – link')->placeholder('/kalkulator atau https://...'),
                Forms\Components\TextInput::make('cta2_label')->label('Tombol kedua – teks'),
                Forms\Components\TextInput::make('cta2_url')->label('Tombol kedua – link'),
            ])->columns(2),
            Forms\Components\Section::make('Penayangan')->schema([
                Forms\Components\DateTimePicker::make('starts_at')->label('Mulai tayang'),
                Forms\Components\DateTimePicker::make('ends_at')->label('Selesai tayang'),
                Fields::sort(),
                Fields::active(),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('public')->label('')->width(120)->height(60),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->description(fn ($record) => $record->subtitle),
                Tables\Columns\IconColumn::make('has_video')->label('Video')->boolean()->state(fn ($record) => filled($record->video) || filled($record->video_url)),
                Tables\Columns\TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('ends_at')->label('Selesai')->dateTime('d M Y')->placeholder('—'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
