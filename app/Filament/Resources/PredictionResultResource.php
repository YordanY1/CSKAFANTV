<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PredictionResultResource\Pages;
use App\Models\PredictionResult;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PredictionResultResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Класация на потребителите';
    protected static ?string $navigationGroup = 'Игри';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                User::query()
                    ->select('users.id', 'users.name')
                    ->selectRaw('COALESCE(SUM(prediction_results.points_awarded), 0) as total_points')
                    ->selectRaw('COUNT(prediction_results.id) as predictions_count')
                    ->leftJoin('predictions', 'users.id', '=', 'predictions.user_id')
                    ->leftJoin('prediction_results', 'predictions.id', '=', 'prediction_results.prediction_id')
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('total_points')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Потребител')->sortable()->searchable(),
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
