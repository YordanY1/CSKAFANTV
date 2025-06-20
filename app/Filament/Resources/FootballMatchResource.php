<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FootballMatchResource\Pages;
use App\Models\FootballMatch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FootballMatchResource extends Resource
{
    protected static ?string $model = FootballMatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Мачове';
    protected static ?string $navigationGroup = 'Футбол';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('home_team_id')
                ->label('Домакин')
                ->relationship('homeTeam', 'name')
                ->required(),

            Forms\Components\Select::make('away_team_id')
                ->label('Гост')
                ->relationship('awayTeam', 'name')
                ->required(),

            Forms\Components\DateTimePicker::make('match_datetime')
                ->label('Дата и час на мача')
                ->seconds(false)
                ->minutesStep(5)
                ->native(false)
                ->displayFormat('d.m.Y H:i')
                ->format('Y-m-d H:i')
                ->timezone('Europe/Sofia')
                ->required(),



            Forms\Components\TextInput::make('stadium')
                ->label('Стадион')
                ->maxLength(255)
                ->nullable(),

            Forms\Components\TextInput::make('home_score')
                ->label('Голове на домакин')
                ->numeric()
                ->nullable(),

            Forms\Components\TextInput::make('away_score')
                ->label('Голове на гост')
                ->numeric()
                ->nullable(),

            Forms\Components\Toggle::make('is_finished')
                ->label('Приключил ли е мачът?')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('homeTeam.name')->label('Домакин')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('awayTeam.name')->label('Гост')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('match_datetime')->label('Дата и час')->dateTime('d.m.Y H:i')->sortable(),
            Tables\Columns\TextColumn::make('stadium')->label('Стадион')->limit(20),
            Tables\Columns\TextColumn::make('home_score')->label('Голове')->numeric(),
            Tables\Columns\TextColumn::make('away_score')->label('-')->numeric(),
            Tables\Columns\IconColumn::make('is_finished')->label('Приключил')->boolean(),
        ])
            ->defaultSort('match_datetime', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFootballMatches::route('/'),
            'create' => Pages\CreateFootballMatch::route('/create'),
            'edit' => Pages\EditFootballMatch::route('/{record}/edit'),
        ];
    }
}
