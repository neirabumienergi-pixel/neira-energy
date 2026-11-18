<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\Fields;
use App\Filament\Concerns\ModuleAccess;
use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    use ModuleAccess;

    protected static string $module = 'content';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $navigationLabel = 'Blog / Artikel';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make([
                Forms\Components\Section::make()->schema([
                    Fields::title(),
                    Fields::slug(),
                    Forms\Components\Textarea::make('excerpt')->label('Ringkasan')->rows(2),
                    Forms\Components\RichEditor::make('content')->label('Isi artikel')
                        ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('posts/content'),
                ]),
                Fields::seo(),
            ])->columnSpan(2),
            Forms\Components\Section::make('Publikasi')->schema([
                Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Terbit'])->default('draft')->required(),
                Forms\Components\DateTimePicker::make('published_at')->label('Tanggal terbit')->default(now()),
                Forms\Components\Select::make('post_category_id')->label('Kategori')->relationship('category', 'name')
                    ->createOptionForm([Forms\Components\TextInput::make('name')->required()])->searchable()->preload(),
                Forms\Components\TagsInput::make('tags'),
                Fields::image('cover', 'Gambar sampul', 'posts'),
                Forms\Components\Select::make('author_id')->label('Penulis')->relationship('author', 'name')->default(fn () => auth()->id()),
            ])->columnSpan(1),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')->disk('public')->label('')->width(80)->height(48),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->wrap()->limit(80),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn ($state) => $state === 'published' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('published_at')->label('Terbit')->dateTime('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('views')->label('Dilihat')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Terbit']),
                Tables\Filters\SelectFilter::make('post_category_id')->label('Kategori')->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('Lihat')->icon('heroicon-o-eye')
                    ->url(fn (Post $record) => route('blog.show', $record))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
