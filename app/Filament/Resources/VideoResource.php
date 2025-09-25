<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Видеогалерия';
    protected static ?string $navigationGroup = 'Медия';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Заглавие')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('tags')
                    ->label('Тагове (разделени със запетая)')
                    ->rows(2)
                    ->placeholder('match,training,interview'),

                Forms\Components\Select::make('category')
                    ->label('Категория')
                    ->options([
                        'Гласът на ФЕНА' => 'Гласът на ФЕНА',
                        'Преди мача' => 'Преди мача',
                        'CSKA FAN TV TALK SHOW' => 'CSKA FAN TV TALK SHOW',
                        'Специални стриймове за членове' => 'Специални стриймове за членове',
                        'Именити червени фенове гостуват' => 'Именити червени фенове гостуват',
                        'Легендите говорят' => 'Легендите говорят',
                        'Червена слава' => 'Червена слава',
                        'Бъдещето на ЦСКА' => 'Бъдещето на ЦСКА',
                        'Децата на ЦСКА' => 'Децата на ЦСКА',
                        'Отговори от гости' => 'Отговори от гости',
                        'Предсезонна подготовка' => 'Предсезонна подготовка',
                        'Извънредни предавания' => 'Извънредни предавания',
                    ])
                    ->searchable()
                    ->required(),

                Forms\Components\Hidden::make('category_slug')
                    ->dehydrated()
                    ->default(fn(Forms\Get $get) => \Str::slug($get('category'))),

                Forms\Components\RichEditor::make('description')
                    ->label('Описание')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'blockquote',
                        'codeBlock',
                        'h2',
                        'h3',
                        'bulletList',
                        'orderedList',
                        'undo',
                        'redo'
                    ])
                    ->columnSpan('full'),

                Forms\Components\TextInput::make('youtube_url')
                    ->label('YouTube линк')
                    ->placeholder('https://www.youtube.com/watch?v=XXXXXXXXXXX')
                    ->url()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Заглавие')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tags')->label('Тагове')->badge(),
                Tables\Columns\TextColumn::make('youtube_url')->label('Линк')->copyable()->limit(40),
                Tables\Columns\TextColumn::make('created_at')->label('Създадено')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Редактирай'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Изтрий избраните'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
