<?php

namespace App\Filament\Resources\GiveawayResource\Pages;

use App\Filament\Resources\GiveawayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGiveaways extends ListRecords
{
    protected static string $resource = GiveawayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
