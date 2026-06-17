<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerRatingResource\Pages\ListPlayerRatings;
use App\Models\FootballMatch;
use App\Models\PlayerReview;
use App\Support\Season;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
        // Само оценките от активния сезон; миналите сезони са в „Архив".
        return PlayerReview::query()
            ->join('football_matches', 'player_reviews.match_id', '=', 'football_matches.id')
            ->where('football_matches.season', '>=', Season::current())
            ->with(['player'])
            ->selectRaw('
                MIN(player_reviews.id) as id,
                player_reviews.player_id,
                player_reviews.match_id,
                ROUND(AVG(player_reviews.rating), 2) as average_rating,
                COUNT(*) as total_votes
            ')
            ->groupBy('player_reviews.player_id', 'player_reviews.match_id');
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

                        if (! $match || ! $match->homeTeam || ! $match->awayTeam) {
                            return 'Непълен мач';
                        }

                        return $match->homeTeam->name.' vs '.$match->awayTeam->name.' ('.$match->match_datetime->format('d.m.Y').')';
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
