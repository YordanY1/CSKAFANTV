<?php

namespace App\Filament\Resources\GiveawayDrawResource\Pages;

use App\Filament\Resources\GiveawayDrawResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGiveawayDraws extends ListRecords
{
    protected static string $resource = GiveawayDrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
