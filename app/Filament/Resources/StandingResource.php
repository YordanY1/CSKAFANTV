<?php


namespace App\Filament\Resources;

use App\Filament\Resources\StandingResource\Pages;
use App\Models\Standing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StandingResource extends Resource
{
    protected static ?string $model = Standing::class;
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'Класиране';
    protected static ?string $navigationGroup = 'Футбол';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('team_id')
                ->label('Отбор')
                ->relationship('team', 'name', fn($query) => $query->orderBy('name'))
                ->required(),

            Forms\Components\TextInput::make('played')->label('Изиграни')->numeric()->default(0)->required(),
            Forms\Components\TextInput::make('wins')->label('Победи')->numeric()->default(0)->required(),
            Forms\Components\TextInput::make('draws')->label('Равенства')->numeric()->default(0)->required(),
            Forms\Components\TextInput::make('losses')->label('Загуби')->numeric()->default(0)->required(),
            Forms\Components\TextInput::make('points')->label('Точки')->numeric()->default(0)->required(),

            Forms\Components\TextInput::make('goals_scored')->label('Вкарани голове')->numeric()->default(0)->required(),
            Forms\Components\TextInput::make('goals_conceded')->label('Допуснати голове')->numeric()->default(0)->required(),

            Forms\Components\TextInput::make('manual_rank')
                ->label('Ръчна позиция')
                ->numeric()
                ->minValue(1)
                ->helperText('Остави празно за автоматично класиране'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('manual_rank')->label('#')->sortable()->badge()->color('info'),
                Tables\Columns\TextColumn::make('team.name')->label('Отбор')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('played')->label('Изиграни'),
                Tables\Columns\TextColumn::make('wins')->label('Победи'),
                Tables\Columns\TextColumn::make('draws')->label('Равенства'),
                Tables\Columns\TextColumn::make('losses')->label('Загуби'),
                Tables\Columns\TextColumn::make('points')->label('Точки')->sortable(),
                Tables\Columns\TextColumn::make('goals_scored')->label('Вкарани'),
                Tables\Columns\TextColumn::make('goals_conceded')->label('Допуснати'),
                Tables\Columns\TextColumn::make('goal_difference')
                    ->label('ГР')
                    ->description('Голова разлика')
                    ->sortable(),
            ])
            ->defaultSort('manual_rank')
            ->defaultSort('points', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStandings::route('/'),
            'create' => Pages\CreateStanding::route('/create'),
            'edit' => Pages\EditStanding::route('/{record}/edit'),
        ];
    }
}
