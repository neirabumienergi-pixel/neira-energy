<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\PortfolioResource\Pages;
use App\Models\Portfolio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PortfolioResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Portfolio::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Portfolio';

    protected static ?string $pluralModelLabel = 'Portfolio Proyek';

    protected static ?int $navigationSort = 4;

    public const SYSTEM_TYPES = ['On Grid' => 'On Grid', 'Off Grid' => 'Off Grid', 'Hybrid' => 'Hybrid', 'PJU' => 'PJU', 'Solusi Terapan' => 'Solusi Terapan', 'Fabrikasi' => 'Fabrikasi', 'Lainnya' => 'Lainnya'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data proyek')->schema([
                Fields::title('title', 'Judul proyek'),
                Fields::slug(),
                Forms\Components\TextInput::make('client_name')->label('Nama klien (opsional)'),
                Forms\Components\Select::make('segment_id')->label('Segmen')->relationship('segment', 'name')->preload(),
                Forms\Components\Select::make('product_category_id')->label('Kategori produk')->relationship('category', 'name')->preload(),
                Forms\Components\Select::make('system_type')->label('Tipe sistem')->options(self::SYSTEM_TYPES),
                Forms\Components\TextInput::make('capacity')->label('Kapasitas')->numeric(),
                Forms\Components\Select::make('capacity_unit')->label('Satuan')->options(['kWp' => 'kWp', 'MWp' => 'MWp', 'titik' => 'titik', 'unit' => 'unit'])->default('kWp'),
                Forms\Components\TextInput::make('location')->label('Lokasi (kota)'),
                Forms\Components\TextInput::make('province')->label('Provinsi'),
                Forms\Components\TextInput::make('year')->label('Tahun')->numeric()->minValue(2000)->maxValue(2100),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('lat')->label('Latitude')->numeric(),
                    Forms\Components\TextInput::make('lng')->label('Longitude')->numeric(),
                ])->columnSpan(1),
                Forms\Components\Textarea::make('summary')->label('Ringkasan')->rows(2)->columnSpanFull(),
                Forms\Components\RichEditor::make('description')->label('Cerita proyek')->columnSpanFull(),
            ])->columns(3),
            Forms\Components\Section::make('Foto')->schema([
                Fields::image('cover', 'Foto utama', 'portfolios')->required(),
                Forms\Components\FileUpload::make('gallery')->label('Galeri')->multiple()->reorderable()->image()
                    ->disk('public')->directory('portfolios')->maxSize(4096),
            ])->columns(2),
            Forms\Components\Section::make()->schema([
                Forms\Components\Toggle::make('is_featured')->label('Tampilkan di beranda'),
                Fields::sort(),
                Fields::active(),
            ])->columns(3),
            Fields::seo(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')->disk('public')->label('')->width(80)->height(52),
                Tables\Columns\TextColumn::make('title')->label('Proyek')->searchable()->description(fn ($record) => $record->location),
                Tables\Columns\TextColumn::make('capacity_label')->label('Kapasitas'),
                Tables\Columns\TextColumn::make('system_type')->label('Tipe')->badge(),
                Tables\Columns\TextColumn::make('segment.name')->label('Segmen'),
                Tables\Columns\IconColumn::make('is_featured')->label('Beranda')->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('system_type')->label('Tipe')->options(self::SYSTEM_TYPES),
                Tables\Filters\SelectFilter::make('segment_id')->label('Segmen')->relationship('segment', 'name'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}
