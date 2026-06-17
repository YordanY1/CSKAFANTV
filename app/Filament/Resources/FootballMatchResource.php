<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FootballMatchResource\Pages;
use App\Models\FootballMatch;
use App\Models\Prediction;
use App\Models\PredictionResult;
use App\Support\Season;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FootballMatchResource extends Resource
{
    protected static ?string $model = FootballMatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Мачове';

    protected static ?string $navigationGroup = 'Футбол';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('home_team_id')
                ->label('Домакин')
                ->relationship('homeTeam', 'name')
                ->required(),

            Forms\Components\Select::make('away_team_id')
                ->label('Гост')
                ->relationship('awayTeam', 'name')
                ->required(),

            Forms\Components\DateTimePicker::make('match_datetime')
                ->label('Дата и час на мача')
                ->seconds(false)
                ->minutesStep(5)
                ->native(false)
                ->displayFormat('d.m.Y H:i')
                ->format('Y-m-d H:i')
                ->timezone('Europe/Sofia')
                ->required()
                ->visible(fn (?FootballMatch $record) => is_null($record)),

            Forms\Components\Select::make('season')
                ->label('Сезон')
                ->options(fn () => Season::formOptions())
                ->default(fn () => Season::current())
                ->required()
                ->searchable()
                ->native(false)
                ->helperText('По подразбиране е текущият сезон. Смени само ако мачът е за друг сезон.'),

            Forms\Components\TextInput::make('stadium')
                ->label('Стадион')
                ->maxLength(255)
                ->nullable(),

            Forms\Components\Fieldset::make('Видео секции')
                ->schema([

                    Forms\Components\Textarea::make('voice_of_the_fan_embed')
                        ->label('🎤 Гласът на ФЕНА')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('before_match_embed')
                        ->label('⏱️ Преди мача')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('talk_show_embed')
                        ->label('🎙️ CSKA FAN TV TALK SHOW')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('member_stream_embed')
                        ->label('🔒 Специални стриймове за членове')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('celebrity_fans_embed')
                        ->label('⭐ Именити червени фенове гостуват')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('legends_speak_embed')
                        ->label('🧓 Легендите говорят')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('red_glory_embed')
                        ->label('🏆 Червена слава')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('cska_future_embed')
                        ->label('🌱 Бъдещето на ЦСКА')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('cska_kids_embed')
                        ->label('👶 Децата на ЦСКА')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('guest_answers_embed')
                        ->label('📣 Отговори от гости')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                    Forms\Components\Textarea::make('preseason_training_embed')
                        ->label('🏋️ Предсезонна подготовка')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Постави целия embed код от YouTube, напр. <iframe ...></iframe>'),

                ])
                ->columns(2)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('youtube_url')
                ->label('YouTube линк (на живо)')
                ->url()
                ->placeholder('https://www.youtube.com/watch?v=...')
                ->nullable(),

            Forms\Components\TextInput::make('home_score')
                ->label('Голове на домакин')
                ->numeric()
                ->nullable(),

            Forms\Components\TextInput::make('away_score')
                ->label('Голове на гост')
                ->numeric()
                ->nullable(),
            Forms\Components\Repeater::make('lineup')
                ->label('Състав')
                ->relationship('lineup')
                ->schema([
                    Forms\Components\Select::make('player_id')
                        ->label('Играч')
                        ->relationship('player', 'name')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Toggle::make('is_starter')
                        ->label('Стартов състав')
                        ->default(true)
                        ->reactive()
                        ->helperText('Ако е изключено, играчът ще бъде считан за резерва.'),

                    Forms\Components\Select::make('replaces_player_id')
                        ->label('Кого замества?')
                        ->options(function () {
                            return \App\Models\Player::pluck('name', 'id')->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->visible(fn ($get) => $get('is_starter') === false)
                        ->helperText('Избери играча, когото този замества.'),

                ])
                ->defaultItems(11)
                ->collapsible()
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_finished')
                ->label('Приключил ли е мачът?')
                ->default(false)
                ->afterStateUpdated(function ($state, $component, $set, $record) {
                    if ($state && $record->home_score !== null && $record->away_score !== null) {
                        $predictions = Prediction::where('football_match_id', $record->id)->get();

                        foreach ($predictions as $prediction) {
                            $points = 0;

                            $hasPrediction = ! is_null($prediction->home_score_prediction) && ! is_null($prediction->away_score_prediction);

                            if ($hasPrediction) {
                                $exact = $prediction->home_score_prediction === $record->home_score &&
                                    $prediction->away_score_prediction === $record->away_score;

                                if ($exact) {
                                    $points = 3;
                                } else {
                                    $points = 1;
                                }
                            }

                            PredictionResult::updateOrCreate(
                                ['prediction_id' => $prediction->id],
                                [
                                    'is_correct' => $points === 3,
                                    'points_awarded' => $points,
                                ]
                            );
                        }
                    }
                }),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('homeTeam.name')->label('Домакин')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('awayTeam.name')->label('Гост')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('match_datetime')->label('Дата и час')->dateTime('d.m.Y H:i')->sortable(),
            Tables\Columns\TextColumn::make('season')->label('Сезон')->badge()->sortable(),
            Tables\Columns\TextColumn::make('stadium')->label('Стадион')->limit(20),
            Tables\Columns\TextColumn::make('home_score')->label('Голове')->numeric(),
            Tables\Columns\TextColumn::make('away_score')->label('-')->numeric(),
            Tables\Columns\IconColumn::make('is_finished')->label('Приключил')->boolean(),
        ])
            ->defaultSort('match_datetime', 'asc')
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
            'index' => Pages\ListFootballMatches::route('/'),
            'create' => Pages\CreateFootballMatch::route('/create'),
            'edit' => Pages\EditFootballMatch::route('/{record}/edit'),
        ];
    }
}
