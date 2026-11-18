<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\ProductPackageResource\Pages;
use App\Models\ProductCategory;
use App\Models\ProductPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductPackageResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'products';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = ProductPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Produk & Layanan';

    protected static ?string $modelLabel = 'Paket Produk';

    protected static ?string $pluralModelLabel = 'Paket Produk';

    protected static ?int $navigationSort = 2;

    public static function categoryOptions(): array
    {
        return ProductCategory::with('parent')->where('has_packages', true)->orderBy('parent_id')->orderBy('sort')->get()
            ->mapWithKeys(fn ($c) => [$c->id => $c->full_name])->all();
    }

    public static function form(Form $form): Form
    {
        $money = fn (string $name, string $label) => Forms\Components\TextInput::make($name)->label($label)->numeric()->prefix('Rp');

        return $form->schema([
            Forms\Components\Tabs::make()->tabs([
                Forms\Components\Tabs\Tab::make('Informasi')->icon('heroicon-o-information-circle')->schema([
                    Forms\Components\Select::make('product_category_id')->label('Kategori')->options(fn () => self::categoryOptions())
                        ->required()->searchable()->live()
                        ->afterStateUpdated(fn (Forms\Set $set, $state) => $set('capacity_unit', ProductCategory::find($state)?->capacity_unit ?? 'kWp')),
                    Fields::title('name', 'Nama paket'),
                    Fields::slug(),
                    Forms\Components\TextInput::make('sku')->label('Kode / SKU'),
                    Forms\Components\TextInput::make('capacity')->label('Kapasitas')->numeric()->required(),
                    Forms\Components\Select::make('capacity_unit')->label('Satuan')->options(['kWp' => 'kWp', 'Watt' => 'Watt', 'kWh' => 'kWh', 'unit' => 'unit'])->default('kWp'),
                    Forms\Components\Select::make('phase')->label('Fasa listrik')->options(['1 Phase' => '1 Phase', '3 Phase' => '3 Phase']),
                    Forms\Components\TextInput::make('warranty')->label('Garansi ringkas')->placeholder('Panel 12 th, Inverter 5 th'),
                    Forms\Components\Textarea::make('short_description')->label('Ringkasan')->rows(2)->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')->label('Deskripsi lengkap')->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Tabs\Tab::make('Gambar & Dokumen')->icon('heroicon-o-photo')->schema([
                    Fields::image('image', 'Gambar utama', 'packages'),
                    Forms\Components\FileUpload::make('gallery')->label('Galeri')->multiple()->reorderable()->image()->disk('public')->directory('packages'),
                    Forms\Components\FileUpload::make('datasheet')->label('Datasheet (PDF)')->disk('public')->directory('datasheets')->acceptedFileTypes(['application/pdf']),
                ])->columns(2),
                Forms\Components\Tabs\Tab::make('Data Teknis')->icon('heroicon-o-cpu-chip')->schema([
                    Forms\Components\TextInput::make('est_production_kwh_month')->label('Estimasi produksi (kWh/bulan)')->numeric(),
                    Forms\Components\TextInput::make('battery_kwh')->label('Kapasitas baterai (kWh)')->numeric(),
                    Forms\Components\TextInput::make('area_required_m2')->label('Kebutuhan area (m²)')->numeric(),
                    Forms\Components\Repeater::make('specifications')->label('Spesifikasi teknis')->schema([
                        Forms\Components\TextInput::make('group')->label('Grup')->placeholder('Panel Surya'),
                        Forms\Components\TextInput::make('label')->label('Parameter')->required(),
                        Forms\Components\TextInput::make('value')->label('Nilai')->required(),
                    ])->columns(3)->reorderable()->collapsible()->columnSpanFull()->addActionLabel('Tambah spesifikasi'),
                    Forms\Components\Repeater::make('components')->label('Komponen / Bill of Material')->schema([
                        Forms\Components\TextInput::make('name')->label('Komponen')->required(),
                        Forms\Components\TextInput::make('brand')->label('Merek / tipe'),
                        Forms\Components\TextInput::make('qty')->label('Qty')->numeric(),
                        Forms\Components\TextInput::make('unit')->label('Satuan')->placeholder('pcs'),
                    ])->columns(4)->reorderable()->collapsible()->columnSpanFull()->addActionLabel('Tambah komponen'),
                    Forms\Components\TagsInput::make('scope_of_work')->label('Lingkup pekerjaan (termasuk)')->columnSpanFull()
                        ->placeholder('Survei, Instalasi, Commissioning, ...'),
                ])->columns(3),
                Forms\Components\Tabs\Tab::make('Harga')->icon('heroicon-o-banknotes')->schema([
                    $money('base_price', 'Harga paket'),
                    $money('strike_price', 'Harga coret (opsional)'),
                    Forms\Components\TextInput::make('price_note')->label('Catatan harga')->placeholder('Belum termasuk PPN 11%'),
                    Forms\Components\Fieldset::make('Rekomendasi kalkulator (opsional – kosong = otomatis dari kapasitas)')->schema([
                        $money('min_monthly_bill', 'Tagihan listrik min / bulan'),
                        $money('max_monthly_bill', 'Tagihan listrik maks / bulan'),
                    ])->columns(2),
                    Forms\Components\Placeholder::make('promo_info')->label('Promo aktif')
                        ->content(function (?ProductPackage $record) {
                            $promo = $record?->bestPromo();

                            return $promo ? $promo->title.' ('.$promo->label.') → Rp '.number_format($record->finalPrice($promo), 0, ',', '.') : 'Tidak ada';
                        })->visibleOn('edit'),
                ])->columns(3),
                Forms\Components\Tabs\Tab::make('Tampilan')->icon('heroicon-o-eye')->schema([
                    Forms\Components\Toggle::make('is_featured')->label('Paket unggulan'),
                    Fields::active(),
                    Fields::sort(),
                    Fields::seo(),
                ])->columns(3),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('category.parent'))
            ->defaultSort('capacity')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('public')->label('')->width(56)->height(56),
                Tables\Columns\TextColumn::make('name')->label('Paket')->searchable()->description(fn ($record) => $record->sku),
                Tables\Columns\TextColumn::make('category.full_name')->label('Kategori')->badge(),
                Tables\Columns\TextColumn::make('capacity')->label('Kapasitas')->sortable()->formatStateUsing(fn ($record) => $record->capacity_label),
                Tables\Columns\TextColumn::make('base_price')->label('Harga')->formatStateUsing(fn ($state) => $state === null ? null : rupiah((float) $state))->sortable(),
                Tables\Columns\TextColumn::make('final_price')->label('Harga promo')
                    ->state(fn (ProductPackage $record) => $record->bestPromo() ? $record->finalPrice() : null)->formatStateUsing(fn ($state) => $state === null ? null : rupiah((float) $state))->placeholder('—')->color('success'),
                Tables\Columns\IconColumn::make('is_featured')->label('Unggulan')->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([Tables\Filters\SelectFilter::make('product_category_id')->label('Kategori')->options(fn () => self::categoryOptions())])
            ->actions([
                Tables\Actions\Action::make('view')->label('Lihat')->icon('heroicon-o-eye')->url(fn (ProductPackage $record) => $record->url())->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()->label('Duplikat')->excludeAttributes(['slug'])
                    ->beforeReplicaSaved(fn ($replica) => $replica->name .= ' (salinan)'),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\BulkAction::make('adjust_price')->label('Ubah harga (%)')->icon('heroicon-o-arrows-up-down')
                    ->form([Forms\Components\TextInput::make('percent')->label('Persen (+/-)')->numeric()->required()])
                    ->action(fn ($records, array $data) => $records->each(fn ($record) => $record->base_price && $record->update(['base_price' => round($record->base_price * (1 + $data['percent'] / 100), -3)])))
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductPackages::route('/'),
            'create' => Pages\CreateProductPackage::route('/create'),
            'edit' => Pages\EditProductPackage::route('/{record}/edit'),
        ];
    }
}
