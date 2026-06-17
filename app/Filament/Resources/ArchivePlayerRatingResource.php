<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchivePlayerRatingResource\Pages\ListArchivePlayerRatings;
use App\Models\PlayerReview;
use App\Support\Season;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ArchivePlayerRatingResource extends Resource
{
    protected static ?string $model = PlayerReview::class;

    protected static ?string $slug = 'archive-player-ratings';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Архив';

    protected static ?string $navigationLabel = 'Оценки на играчи';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Архив – Оценки на играчи';

    protected static ?string $modelLabel = 'оценка';

    public static function getEloquentQuery(): Builder
    {
        return PlayerReview::query()
            ->join('football_matches', 'player_reviews.match_id', '=', 'football_matches.id')
            ->with('player')
            ->whereNotNull('football_matches.season')
            ->where('football_matches.season', '<', Season::current())
            ->selectRaw('
                MIN(player_reviews.id) as id,
                player_reviews.player_id,
                football_matches.season as season,
                ROUND(AVG(player_reviews.rating), 2) as average_rating,
                COUNT(*) as total_votes
            ')
            ->groupBy('player_reviews.player_id', 'football_matches.season');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('season')->label('Сезон')->badge()->sortable(),
                Tables\Columns\TextColumn::make('player.name')->label('Играч / Треньор')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('average_rating')->label('Средна оценка')->sortable(),
                Tables\Columns\TextColumn::make('total_votes')->label('Гласове')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('season')
                    ->label('Сезон')
                    ->options(fn () => Season::options())
                    ->default(Season::latestArchived())
                    ->query(fn (Builder $query, array $data) => filled($data['value'])
                        ? $query->where('football_matches.season', $data['value'])
                        : $query),
            ])
            ->defaultSort('average_rating', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArchivePlayerRatings::route('/'),
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
        return false;
    }
}
