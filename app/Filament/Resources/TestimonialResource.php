<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Testimoni';

    protected static ?string $pluralModelLabel = 'Testimoni';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')->label('Nama')->required(),
                Forms\Components\TextInput::make('position')->label('Jabatan'),
                Forms\Components\TextInput::make('company')->label('Perusahaan / Instansi'),
                Forms\Components\Select::make('rating')->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★'])->default(5),
                Forms\Components\Textarea::make('content')->label('Isi testimoni')->required()->rows(4)->columnSpanFull(),
                Fields::image('photo', 'Foto', 'testimonials')->avatar(),
                Fields::sort(),
                Fields::active(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->disk('public')->circular(),
                Tables\Columns\TextColumn::make('name')->label('Nama')->description(fn ($record) => trim($record->position.' · '.$record->company, ' ·'))->searchable(),
                Tables\Columns\TextColumn::make('content')->label('Testimoni')->limit(70),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
