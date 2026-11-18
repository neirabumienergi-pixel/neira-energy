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
