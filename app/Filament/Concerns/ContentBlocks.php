<?php

namespace App\Filament\Concerns;

use App\Models\Faq;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/**
 * Section builder untuk brosur kategori produk & halaman statis.
 * Setiap blok dirender oleh resources/views/blocks/{type}.blade.php
 */
class ContentBlocks
{
    public static function make(string $name = 'blocks', bool $forBrochure = false): Builder
    {
        $blocks = [
            Block::make('hero')->label('Hero / Sampul')->icon('heroicon-o-sparkles')->schema([
                TextInput::make('eyebrow')->label('Label kecil'),
                TextInput::make('title')->label('Judul')->required(),
                Textarea::make('text')->label('Teks')->rows(3),
                self::img('image', 'Gambar latar'),
                TextInput::make('cta_label')->label('Tombol – teks'),
                TextInput::make('cta_url')->label('Tombol – link'),
            ])->columns(2),
            Block::make('text')->label('Teks + Gambar')->icon('heroicon-o-document-text')->schema([
                TextInput::make('heading')->label('Judul'),
                RichEditor::make('body')->label('Isi')->columnSpanFull(),
                self::img('image', 'Gambar'),
                Select::make('image_position')->label('Posisi gambar')->options(['right' => 'Kanan', 'left' => 'Kiri'])->default('right'),
            ])->columns(2),
            Block::make('features')->label('Keunggulan / Fitur')->icon('heroicon-o-check-badge')->schema([
                TextInput::make('heading')->label('Judul'),
                Textarea::make('intro')->label('Pengantar')->rows(2),
                Repeater::make('items')->label('Poin')->schema([
                    TextInput::make('icon')->label('Ikon heroicon')->placeholder('heroicon-o-bolt'),
                    TextInput::make('title')->label('Judul')->required(),
                    Textarea::make('text')->label('Teks')->rows(2),
                ])->columns(3)->collapsible()->columnSpanFull(),
            ])->columns(2),
            Block::make('stats')->label('Angka / Statistik')->icon('heroicon-o-chart-bar')->schema([
                TextInput::make('heading')->label('Judul'),
                Repeater::make('items')->label('Angka')->schema([
                    TextInput::make('value')->label('Nilai')->required()->placeholder('10+ MWp'),
                    TextInput::make('label')->label('Keterangan')->required(),
                ])->columns(2)->grid(2),
            ]),
            Block::make('specs')->label('Tabel Spesifikasi')->icon('heroicon-o-table-cells')->schema([
                TextInput::make('heading')->label('Judul'),
                Repeater::make('rows')->label('Baris')->schema([
                    TextInput::make('label')->label('Parameter')->required(),
                    TextInput::make('value')->label('Nilai')->required(),
                ])->columns(2),
            ]),
            Block::make('steps')->label('Alur / Tahapan')->icon('heroicon-o-arrow-trending-up')->schema([
                TextInput::make('heading')->label('Judul'),
                Repeater::make('items')->label('Tahap')->schema([
                    TextInput::make('title')->label('Judul')->required(),
                    Textarea::make('text')->label('Teks')->rows(2),
                ])->columns(2),
            ]),
            Block::make('gallery')->label('Galeri Foto')->icon('heroicon-o-photo')->schema([
                TextInput::make('heading')->label('Judul'),
                FileUpload::make('images')->label('Foto')->multiple()->reorderable()->image()->disk('public')->directory('blocks'),
            ]),
            Block::make('faq')->label('FAQ')->icon('heroicon-o-question-mark-circle')->schema([
                TextInput::make('heading')->label('Judul')->default('Pertanyaan Umum'),
                Select::make('group')->label('Kelompok FAQ')->options(fn () => Faq::query()->distinct()->pluck('group', 'group')->all()),
            ])->columns(2),
            Block::make('cta')->label('Ajakan (CTA)')->icon('heroicon-o-megaphone')->schema([
                TextInput::make('title')->label('Judul')->required(),
                Textarea::make('text')->label('Teks')->rows(2),
                TextInput::make('button_label')->label('Tombol – teks'),
                TextInput::make('button_url')->label('Tombol – link')->helperText('Kosongkan untuk link WhatsApp otomatis.'),
            ])->columns(2),
            Block::make('html')->label('HTML Kustom')->icon('heroicon-o-code-bracket')->schema([
                Textarea::make('html')->label('Kode HTML')->rows(10)->helperText('Untuk tata letak khusus. Tailwind class bisa dipakai.'),
            ]),
        ];

        if ($forBrochure) {
            array_splice($blocks, 4, 0, [
                Block::make('packages')->label('Tabel Paket (otomatis)')->icon('heroicon-o-cube')->schema([
                    TextInput::make('heading')->label('Judul')->default('Pilihan Paket'),
                    Textarea::make('intro')->label('Pengantar')->rows(2),
                    Select::make('show_price')->label('Tampilkan harga')->options(['1' => 'Ya', '0' => 'Tidak'])->default('1'),
                ])->columns(2),
                Block::make('subcategories')->label('Daftar Sub-kategori (otomatis)')->icon('heroicon-o-squares-2x2')->schema([
                    TextInput::make('heading')->label('Judul')->default('Pilihan Sistem'),
                ]),
            ]);
        }

        return Builder::make($name)->hiddenLabel()->blocks($blocks)
            ->collapsible()->cloneable()->blockNumbers(false)->addActionLabel('Tambah section');
    }

    protected static function img(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)->label($label)->image()->disk('public')->directory('blocks')->maxSize(4096);
    }
}
