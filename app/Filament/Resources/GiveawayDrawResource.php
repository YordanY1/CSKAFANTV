<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiveawayDrawResource\Pages;
use App\Models\GiveawayDraw;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class GiveawayDrawResource extends Resource
{
    protected static ?string $model = GiveawayDraw::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Игри';
    protected static ?string $pluralModelLabel = 'Победители от томболи';


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Победител')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Имейл')
                    ->searchable(),

                TextColumn::make('drawn_at')
                    ->label('Изтеглен на')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('drawn_at', 'desc')
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGiveawayDraws::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
