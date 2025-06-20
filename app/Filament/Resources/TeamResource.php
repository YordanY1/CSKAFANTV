<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;


class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Клубове';
    protected static ?string $navigationGroup = 'Футбол';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Име на клуба')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('logo')
                    ->label('Лого')
                    ->image()
                    ->disk('public')
                    ->directory('team-logos')
                    ->nullable(),

                Forms\Components\TextInput::make('country')
                    ->label('Държава')
                    ->nullable(),

                Forms\Components\TextInput::make('founded_at')
                    ->label('Основан през')
                    ->numeric()
                    ->nullable(),

                Forms\Components\TextInput::make('stadium')
                    ->label('Стадион')
                    ->nullable(),

                Forms\Components\TextInput::make('manager')
                    ->label('Треньор')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable()->label('Клуб'),
                Tables\Columns\ImageColumn::make('logo')
                    ->disk('public')
                    ->label('Лого')
                    ->circular()
                    ->height(40),
                Tables\Columns\TextColumn::make('founded_at')->label('Година'),
                Tables\Columns\TextColumn::make('stadium')->label('Стадион'),
                Tables\Columns\TextColumn::make('manager')->label('Треньор'),
            ])
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
