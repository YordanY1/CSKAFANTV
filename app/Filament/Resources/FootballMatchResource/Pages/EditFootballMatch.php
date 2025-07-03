<?php

namespace App\Filament\Resources\FootballMatchResource\Pages;

use App\Filament\Resources\FootballMatchResource;
use App\Models\FootballMatch;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\MatchLineup;
use Illuminate\Support\Facades\Log;

class EditFootballMatch extends EditRecord
{
    protected static string $resource = FootballMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
