<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchiveHallOfFameResource\Pages\ListArchiveHallOfFame;
use App\Models\MonthlyPlayerAward;
use App\Support\Season;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ArchiveHallOfFameResource extends Resource
{
    protected static ?string $model = MonthlyPlayerAward::class;

    protected static ?string $slug = 'archive-hall-of-fame';

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Архив';

    protected static ?string $navigationLabel = 'Зала на славата';

    protected static ?int $navigationSort = 3;

    protected static ?string $pluralModelLabel = 'Архив – Зала на славата';

    protected static ?string $modelLabel = 'играч на месеца';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('season')
                    ->label('Сезон')
                    ->badge()
                    ->state(fn (MonthlyPlayerAward $record) => Season::fromYearMonth($record->year, $record->month)),
                Tables\Columns\TextColumn::make('player.name')->label('Играч')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('month')->label('Месец')->sortable(),
                Tables\Columns\TextColumn::make('year')->label('Година')->sortable(),
                Tables\Columns\TextColumn::make('average_rating')->label('Средна оценка')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('season')
                    ->label('Сезон')
                    ->options(fn () => Season::options())
                    ->default(Season::latest())
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'])) {
                            return $query;
                        }

                        [$startIndex, $endIndex] = Season::monthIndexBounds($data['value']);

                        return $query
                            ->whereRaw('(year * 12 + month) >= ?', [$startIndex])
                            ->whereRaw('(year * 12 + month) < ?', [$endIndex]);
                    }),
            ])
            ->defaultSort('year', 'desc')
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
            'index' => ListArchiveHallOfFame::route('/'),
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
