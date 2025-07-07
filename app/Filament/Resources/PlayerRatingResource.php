<?php

namespace App\Filament\Resources;

use App\Models\PlayerReview;
use App\Models\FootballMatch;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\PlayerRatingResource\Pages\ListPlayerRatings;

class PlayerRatingResource extends Resource
{
    protected static ?string $model = PlayerReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Оценки по мачове';
    protected static ?string $pluralModelLabel = 'Оценки на играчи';
    protected static ?string $modelLabel = 'Оценка';
    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return PlayerReview::query()
            ->with(['player'])
            ->selectRaw('
                MIN(id) as id,
                player_id,
                match_id,
                ROUND(AVG(rating), 2) as average_rating,
                COUNT(*) as total_votes
            ')
            ->groupBy('player_id', 'match_id');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('player.name')
                    ->label('Играч / Треньор')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('match_id')
                    ->label('Мач')
                    ->formatStateUsing(function ($state, $record) {
                        $match = FootballMatch::with(['homeTeam', 'awayTeam'])->find($record->match_id);

                        if (!$match || !$match->homeTeam || !$match->awayTeam) {
                            return 'Непълен мач';
                        }

                        return $match->homeTeam->name . ' vs ' . $match->awayTeam->name . ' (' . $match->match_datetime->format('d.m.Y') . ')';
                    }),

                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Средна оценка')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_votes')
                    ->label('Гласове')
                    ->sortable(),
            ])


            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('match_id', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayerRatings::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }
}
