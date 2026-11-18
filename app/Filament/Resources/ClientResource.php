<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Klien / Partner';

    protected static ?string $pluralModelLabel = 'Klien & Partner';

    protected static ?int $navigationSort = 7;

    public const TYPES = ['client' => 'Klien', 'partner' => 'Partner', 'supplier' => 'Supplier / Brand'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')->label('Nama')->required(),
                Forms\Components\Select::make('type')->label('Tipe')->options(self::TYPES)->default('client')->required(),
                Fields::image('logo', 'Logo (PNG transparan)', 'clients'),
                Forms\Components\TextInput::make('url')->label('Website')->url(),
                Fields::sort(),
                Fields::active(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->disk('public')->height(36),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Tipe')->badge()->formatStateUsing(fn ($state) => self::TYPES[$state] ?? $state),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([Tables\Filters\SelectFilter::make('type')->options(self::TYPES)])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
