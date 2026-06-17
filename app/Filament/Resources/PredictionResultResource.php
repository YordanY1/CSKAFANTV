<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PredictionResultResource\Pages;
use App\Models\User;
use App\Support\Season;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PredictionResultResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Класация на потребителите';

    protected static ?string $pluralModelLabel = 'Класация на потребителите';

    protected static ?string $modelLabel = 'класиране';

    protected static ?string $navigationGroup = 'Игри';

    public static function rankingQuery(): Builder
    {
        // Класацията показва само активния сезон; миналите сезони са в „Архив".
        return User::query()
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('COALESCE(SUM(prediction_results.points_awarded), 0) as total_points')
            ->selectRaw('COUNT(prediction_results.id) as predictions_count')
            ->join('predictions', 'users.id', '=', 'predictions.user_id')
            ->join('prediction_results', 'predictions.id', '=', 'prediction_results.prediction_id')
            ->join('football_matches', 'predictions.football_match_id', '=', 'football_matches.id')
            ->where('football_matches.season', Season::current())
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_points');
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(static::rankingQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Потребител')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Имейл')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('total_points')->label('Точки')->sortable(),
                Tables\Columns\TextColumn::make('predictions_count')->label('Брой прогнози')->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPredictionResults::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
