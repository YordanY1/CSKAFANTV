<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchivePredictionRankingResource\Pages\ListArchivePredictionRankings;
use App\Models\PredictionResult;
use App\Support\Season;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ArchivePredictionRankingResource extends Resource
{
    protected static ?string $model = PredictionResult::class;

    protected static ?string $slug = 'archive-prediction-rankings';

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Архив';

    protected static ?string $navigationLabel = 'Класиране по прогнози';

    protected static ?int $navigationSort = 4;

    protected static ?string $pluralModelLabel = 'Архив – Класиране по прогнози';

    protected static ?string $modelLabel = 'класиране';

    public static function getEloquentQuery(): Builder
    {
        return PredictionResult::query()
            ->join('predictions', 'prediction_results.prediction_id', '=', 'predictions.id')
            ->join('users', 'predictions.user_id', '=', 'users.id')
            ->join('football_matches', 'predictions.football_match_id', '=', 'football_matches.id')
            ->whereNotNull('football_matches.season')
            ->where('football_matches.season', '<', Season::current())
            ->selectRaw('
                MIN(prediction_results.id) as id,
                users.id as user_id,
                users.name as user_name,
                users.email as user_email,
                football_matches.season as season,
                SUM(prediction_results.points_awarded) as total_points,
                COUNT(DISTINCT predictions.football_match_id) as attempts
            ')
            ->groupBy('users.id', 'users.name', 'users.email', 'football_matches.season');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('season')->label('Сезон')->badge()->sortable(),
                Tables\Columns\TextColumn::make('user_name')->label('Потребител')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user_email')->label('Имейл')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('attempts')->label('Опити')->sortable(),
                Tables\Columns\TextColumn::make('total_points')->label('Точки')->sortable(),
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
            ->defaultSort('total_points', 'desc')
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
            'index' => ListArchivePredictionRankings::route('/'),
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
