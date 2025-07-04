<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerResource\Pages;
use App\Models\Player;
use App\Models\Team;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Играчите';
    protected static ?string $pluralLabel = 'Играчите';
    protected static ?string $modelLabel = 'Играч';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Футбол';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                FileUpload::make('image_path')
                    ->label('Снимка')
                    ->image()
                    ->directory('players')
                    ->imageEditor()
                    ->imagePreviewHeight('150')
                    ->columnSpan('full'),

                TextInput::make('name')
                    ->label('Име')
                    ->required(),

                TextInput::make('number')
                    ->label('Номер')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(99),

                TextInput::make('position')
                    ->label('Позиция'),

                Select::make('team_id')
                    ->label('Отбор')
                    ->relationship('team', 'name')
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Снимка')
                    ->circular()
                    ->height(40),

                TextColumn::make('name')->label('Име')->searchable(),
                TextColumn::make('number')->label('№'),
                TextColumn::make('position')->label('Позиция'),
                TextColumn::make('team.name')->label('Отбор')->searchable(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayers::route('/'),
            'create' => Pages\CreatePlayer::route('/create'),
            'edit' => Pages\EditPlayer::route('/{record}/edit'),
        ];
    }
}
