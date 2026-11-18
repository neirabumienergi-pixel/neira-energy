<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQ';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('group')->label('Kelompok')
                    ->options(fn () => Faq::query()->distinct()->pluck('group', 'group')->all() + ['Umum' => 'Umum'])
                    ->searchable()->createOptionForm([Forms\Components\TextInput::make('group')->required()])
                    ->createOptionUsing(fn (array $data) => $data['group'])
                    ->default('Umum')->required(),
                Fields::sort(),
                Forms\Components\TextInput::make('question')->label('Pertanyaan')->required()->columnSpanFull(),
                Forms\Components\RichEditor::make('answer')->label('Jawaban')->required()->columnSpanFull(),
                Fields::active(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('group')->label('Kelompok')->badge()->sortable(),
                Tables\Columns\TextColumn::make('question')->label('Pertanyaan')->searchable()->wrap(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([Tables\Filters\SelectFilter::make('group')->label('Kelompok')->options(fn () => Faq::query()->distinct()->pluck('group', 'group')->all())])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
