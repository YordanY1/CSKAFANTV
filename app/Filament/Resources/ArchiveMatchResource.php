<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchiveMatchResource\Pages\ListArchiveMatches;
use App\Models\FootballMatch;
use App\Support\Season;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ArchiveMatchResource extends Resource
{
    protected static ?string $model = FootballMatch::class;

    protected static ?string $slug = 'archive-matches';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Архив';

    protected static ?string $navigationLabel = 'Мачове';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Архив – Мачове';

    protected static ?string $modelLabel = 'мач';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_finished', true)
            ->whereNotNull('season');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('season')->label('Сезон')->badge()->sortable(),
                Tables\Columns\TextColumn::make('homeTeam.name')->label('Домакин')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('awayTeam.name')->label('Гост')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('match_datetime')->label('Дата и час')->dateTime('d.m.Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('home_score')->label('Голове')->numeric(),
                Tables\Columns\TextColumn::make('away_score')->label('-')->numeric(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('season')
                    ->label('Сезон')
                    ->options(fn () => Season::options())
                    ->default(Season::latest()),
            ])
            ->defaultSort('match_datetime', 'desc')
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
            'index' => ListArchiveMatches::route('/'),
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
