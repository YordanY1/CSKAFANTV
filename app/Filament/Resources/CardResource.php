<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CardResource\Pages;
use App\Filament\Resources\CardResource\RelationManagers;
use App\Models\Card;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
       protected static ?string $navigationLabel = 'Картони';
    protected static ?string $navigationGroup = 'Футбол';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('player_id')
                    ->label('Играч')
                    ->relationship('player', 'name')
                    ->required(),

                Forms\Components\TextInput::make('yellow_cards')
                    ->label('Жълти картони')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('red_cards')
                    ->label('Червени картони (директни)')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('second_yellow_reds')
                    ->label('ЧК от 2 ЖК')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('player.name')->label('Играч')->searchable(),
                Tables\Columns\TextColumn::make('yellow_cards')->label('ЖК')->sortable(),
                Tables\Columns\TextColumn::make('red_cards')->label('ЧК (директни)')->sortable(),
                Tables\Columns\TextColumn::make('second_yellow_reds')->label('ЧК от 2 ЖК')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
