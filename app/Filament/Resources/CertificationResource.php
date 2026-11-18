<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\CertificationResource\Pages;
use App\Models\Certification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CertificationResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $model = Certification::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Legalitas / Sertifikasi';

    protected static ?string $pluralModelLabel = 'Legalitas & Sertifikasi';

    protected static ?int $navigationSort = 9;

    public const STATUSES = ['active' => 'Berlaku', 'in_process' => 'Dalam proses'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')->label('Nama legalitas / sertifikat')->required(),
                Forms\Components\TextInput::make('number')->label('Nomor'),
                Forms\Components\TextInput::make('issuer')->label('Penerbit'),
                Forms\Components\Select::make('status')->options(self::STATUSES)->default('active')->required(),
                Forms\Components\Textarea::make('description')->label('Keterangan')->rows(2)->columnSpanFull(),
                Fields::image('logo', 'Logo', 'certifications'),
                Forms\Components\FileUpload::make('file')->label('Dokumen (PDF/gambar)')->disk('public')->directory('certifications')
                    ->acceptedFileTypes(['application/pdf', 'image/*']),
                Forms\Components\Toggle::make('show_file')->label('Dokumen boleh diunduh publik'),
                Fields::sort(),
                Fields::active(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->description(fn ($record) => $record->number),
                Tables\Columns\TextColumn::make('issuer')->label('Penerbit'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => self::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => $state === 'active' ? 'success' : 'warning'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Tampil'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertifications::route('/'),
            'create' => Pages\CreateCertification::route('/create'),
            'edit' => Pages\EditCertification::route('/{record}/edit'),
        ];
    }
}
