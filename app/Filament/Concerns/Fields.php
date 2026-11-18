<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

/** Komponen form yang dipakai berulang di banyak resource. */
class Fields
{
    public static function title(string $name = 'title', string $label = 'Judul'): TextInput
    {
        return TextInput::make($name)->label($label)->required()->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                if ($operation === 'create' || blank($get('slug'))) {
                    $set('slug', Str::slug((string) $state));
                }
            });
    }

    public static function slug(): TextInput
    {
        return TextInput::make('slug')->label('Slug (URL)')->maxLength(255)
            ->helperText('Kosongkan untuk dibuat otomatis dari judul.')
            ->unique(ignoreRecord: true);
    }

    public static function image(string $name = 'image', string $label = 'Gambar', string $dir = 'uploads'): FileUpload
    {
        return FileUpload::make($name)->label($label)->image()->imageEditor()
            ->disk('public')->directory($dir)->maxSize(4096);
    }

    public static function video(string $name = 'video', string $label = 'Video latar (MP4/WebM)', string $dir = 'videos'): FileUpload
    {
        return FileUpload::make($name)->label($label)->disk('public')->directory($dir)
            ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(51200)
            ->helperText('Disarankan: MP4 H.264, 1920×1080, 10–20 detik, tanpa suara, < 10 MB. Gambar tetap dipakai sebagai poster & untuk mobile.');
    }

    public static function seo(): Section
    {
        return Section::make('SEO')->description('Opsional. Jika kosong, judul & ringkasan dipakai.')
            ->schema([
                TextInput::make('seo.meta_title')->label('Meta title')->maxLength(70),
                Textarea::make('seo.meta_description')->label('Meta description')->rows(2)->maxLength(170),
                FileUpload::make('seo.og_image')->label('Gambar share (OG)')->image()->disk('public')->directory('seo'),
            ])->collapsed()->columns(1);
    }

    public static function active(string $label = 'Aktif / tampil'): Toggle
    {
        return Toggle::make('is_active')->label($label)->default(true);
    }

    public static function sort(): TextInput
    {
        return TextInput::make('sort')->label('Urutan')->numeric()->default(0);
    }
}
