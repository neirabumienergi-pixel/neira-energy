<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\PromoResource\Pages;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'promos';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Produk & Layanan';

    protected static ?string $modelLabel = 'Promo';

    protected static ?string $pluralModelLabel = 'Promo';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Promo')->schema([
                Fields::title('title', 'Judul promo'),
                Fields::slug(),
                Forms\Components\TextInput::make('code')->label('Kode promo (opsional)')->unique(ignoreRecord: true),
                Fields::image('image', 'Banner promo', 'promos'),
                Forms\Components\Textarea::make('short_description')->label('Ringkasan')->rows(2)->columnSpanFull(),
                Forms\Components\RichEditor::make('content')->label('Detail & syarat ketentuan')->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Potongan')->schema([
                Forms\Components\Select::make('discount_type')->label('Jenis')->options(['percent' => 'Persen (%)', 'fixed' => 'Nominal (Rp)'])->default('percent')->live(),
                Forms\Components\TextInput::make('discount_value')->label('Nilai')->numeric()->required()
                    ->suffix(fn (Get $get) => $get('discount_type') === 'percent' ? '%' : null)
                    ->prefix(fn (Get $get) => $get('discount_type') === 'fixed' ? 'Rp' : null),
                Forms\Components\TextInput::make('max_discount')->label('Maks. potongan')->numeric()->prefix('Rp')
                    ->visible(fn (Get $get) => $get('discount_type') === 'percent'),
                Forms\Components\Select::make('applies_to')->label('Berlaku untuk')->options([
                    'all' => 'Semua paket', 'categories' => 'Kategori tertentu', 'packages' => 'Paket tertentu',
                ])->default('all')->live(),
                Forms\Components\Select::make('categories')->label('Kategori')->relationship('categories', 'name')->multiple()->preload()
                    ->visible(fn (Get $get) => $get('applies_to') === 'categories'),
                Forms\Components\Select::make('packages')->label('Paket')->relationship('packages', 'name')->multiple()->preload()->searchable()
                    ->visible(fn (Get $get) => $get('applies_to') === 'packages'),
            ])->columns(3),
            Forms\Components\Section::make('Periode')->schema([
                Forms\Components\DateTimePicker::make('starts_at')->label('Mulai'),
                Forms\Components\DateTimePicker::make('ends_at')->label('Berakhir'),
                Forms\Components\TextInput::make('quota')->label('Kuota (opsional)')->numeric(),
                Forms\Components\Toggle::make('show_on_site')->label('Tampil di halaman Promo')->default(true),
                Fields::active(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('public')->label('')->width(80)->height(45),
                Tables\Columns\TextColumn::make('title')->label('Promo')->searchable()->description(fn ($record) => $record->code),
                Tables\Columns\TextColumn::make('label')->label('Potongan')->badge()->color('success'),
                Tables\Columns\TextColumn::make('applies_to')->label('Berlaku')
                    ->formatStateUsing(fn ($state) => ['all' => 'Semua', 'categories' => 'Kategori', 'packages' => 'Paket'][$state] ?? $state),
                Tables\Columns\TextColumn::make('ends_at')->label('Berakhir')->dateTime('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('running')->label('Status')->badge()
                    ->state(fn (Promo $record) => $record->isRunning() ? 'Berjalan' : 'Tidak aktif')
                    ->color(fn ($state) => $state === 'Berjalan' ? 'success' : 'gray'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }
}
