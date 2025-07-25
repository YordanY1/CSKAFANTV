<?php

namespace App\Filament\Resources\FootballMatchResource\Pages;

use App\Filament\Resources\FootballMatchResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Pages\Actions\Action;
use Filament\Actions;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Filament\Notifications\Notification;

class EditFootballMatch extends EditRecord
{
    protected static string $resource = FootballMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Action::make('copy_obs_link')
                ->label('🎥 OBS линк')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->action(function () {
                    $match = $this->record;
                    $token = hash_hmac('sha256', $match->slug, config('app.key'));

                    $url = route('obs.match', [
                        'slug' => $match->slug,
                        'token' => $token,
                    ]);

                    Notification::make()
                        ->title('OBS линк генериран')
                        ->body("Копирай линка в OBS браузър сорс: <br><code style='word-break: break-all;'>{$url}</code>")
                        ->success()
                        ->duration(10000)
                        ->send();
                }),
        ];
    }
}
