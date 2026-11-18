<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\GalleryResource\Pages;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Album';

    protected static ?string $pluralModelLabel = 'Galeri';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Album')->schema([
                Fields::title('title', 'Judul album'),
                Fields::slug(),
                Forms\Components\DatePicker::make('event_date')->label('Tanggal kegiatan'),
                Fields::image('cover', 'Sampul (opsional – default foto pertama)', 'galleries'),
                Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(2)->columnSpanFull(),
                Fields::sort(),
                Fields::active(),
            ])->columns(2),
            Forms\Components\Section::make('Isi album')->schema([
                Forms\Components\Repeater::make('items')->hiddenLabel()->schema([
                    Forms\Components\Select::make('type')->label('Jenis')->options(['image' => 'Foto', 'video' => 'Video (YouTube)'])->default('image')->live(),
                    Fields::image('image', 'Foto', 'galleries')->visible(fn (Get $get) => $get('type') !== 'video'),
                    Forms\Components\TextInput::make('video_url')->label('Link YouTube')->url()->visible(fn (Get $get) => $get('type') === 'video'),
                    Forms\Components\TextInput::make('caption')->label('Keterangan'),
                ])->columns(3)->reorderable()->collapsible()->grid(1)->addActionLabel('Tambah foto / video')->defaultItems(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')->disk('public')->label('')->width(80)->height(52),
                Tables\Columns\TextColumn::make('title')->label('Album')->searchable(),
                Tables\Columns\TextColumn::make('items')->label('Item')->formatStateUsing(fn ($record) => count($record->items ?? [])),
                Tables\Columns\TextColumn::make('event_date')->label('Tanggal')->date('d M Y'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
