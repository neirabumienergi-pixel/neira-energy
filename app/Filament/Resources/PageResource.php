<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\ContentBlocks;
use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Halaman';

    protected static ?string $pluralModelLabel = 'Halaman Statis';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Fields::title(),
                Fields::slug(),
                Forms\Components\TextInput::make('subtitle')->label('Subjudul'),
                Fields::image('hero_image', 'Gambar header', 'pages'),
                Forms\Components\Toggle::make('show_in_menu')->label('Tampil di menu'),
                Fields::sort(),
                Fields::active(),
            ])->columns(2),
            Forms\Components\Section::make('Isi halaman')->schema([ContentBlocks::make('blocks')]),
            Fields::seo(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('slug')->prefix('/halaman/'),
                Tables\Columns\IconColumn::make('show_in_menu')->label('Menu')->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('Lihat')->icon('heroicon-o-eye')->url(fn (Page $record) => route('page.show', $record))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
