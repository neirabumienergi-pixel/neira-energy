<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\ContentBlocks;
use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductCategoryResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'products';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Produk & Layanan';

    protected static ?string $modelLabel = 'Kategori Produk';

    protected static ?string $pluralModelLabel = 'Kategori & Brosur';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->tabs([
                Forms\Components\Tabs\Tab::make('Informasi')->icon('heroicon-o-information-circle')->schema([
                    Fields::title('name', 'Nama kategori'),
                    Fields::slug(),
                    Forms\Components\Select::make('parent_id')->label('Induk kategori')
                        ->relationship('parent', 'name', fn (Builder $query, ?ProductCategory $record) => $query->whereNull('parent_id')->when($record, fn ($q) => $q->whereKeyNot($record->id)))
                        ->placeholder('— Kategori utama —'),
                    Forms\Components\TextInput::make('tagline'),
                    Forms\Components\Textarea::make('short_description')->label('Ringkasan')->rows(3)->columnSpanFull(),
                    Forms\Components\TextInput::make('icon')->label('Ikon (heroicon)')->placeholder('heroicon-o-sun'),
                    Fields::image('image', 'Gambar kategori', 'categories'),
                    Forms\Components\Toggle::make('has_packages')->label('Memiliki paket produk (per kapasitas)')->live(),
                    Forms\Components\Select::make('capacity_unit')->label('Satuan kapasitas paket')
                        ->options(['kWp' => 'kWp', 'Watt' => 'Watt', 'kWh' => 'kWh', 'unit' => 'unit'])
                        ->visible(fn (Forms\Get $get) => $get('has_packages')),
                    Forms\Components\TextInput::make('external_url')->label('Link eksternal (mis. Energy Mart)')->url()
                        ->helperText('Jika diisi, kategori diarahkan ke link ini.'),
                    Fields::sort(),
                    Fields::active(),
                ])->columns(2),
                Forms\Components\Tabs\Tab::make('Brosur (HTML)')->icon('heroicon-o-document-text')->schema([
                    Forms\Components\Placeholder::make('brochure_help')->hiddenLabel()
                        ->content('Susun brosur umum kategori ini. Tampil di halaman kategori & versi cetak/PDF di /produk/{slug}/brosur. Blok "Tabel Paket" menarik data paket otomatis.'),
                    ContentBlocks::make('brochure', forBrochure: true),
                ]),
                Forms\Components\Tabs\Tab::make('SEO')->icon('heroicon-o-magnifying-glass')->schema([Fields::seo()->collapsed(false)]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('parent')->withCount('packages'))
            ->defaultSort('sort')->reorderable('sort')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Kategori')->searchable()
                    ->formatStateUsing(fn (ProductCategory $record) => $record->parent ? '↳ '.$record->name : $record->name)
                    ->weight(fn (ProductCategory $record) => $record->parent ? null : 'bold')
                    ->description(fn (ProductCategory $record) => $record->parent?->name),
                Tables\Columns\IconColumn::make('has_packages')->label('Paket')->boolean(),
                Tables\Columns\TextColumn::make('packages_count')->label('Jml paket'),
                Tables\Columns\TextColumn::make('brochure')->label('Brosur')
                    ->formatStateUsing(fn ($record) => count($record->brochure ?? []).' section'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([Tables\Filters\SelectFilter::make('parent_id')->label('Induk')->relationship('parent', 'name')])
            ->actions([
                Tables\Actions\Action::make('brochure')->label('Brosur')->icon('heroicon-o-printer')
                    ->url(fn (ProductCategory $record) => route('products.brochure', $record))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()->excludeAttributes(['slug', 'packages_count'])
                    ->beforeReplicaSaved(fn ($replica) => $replica->name .= ' (salinan)'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }
}
