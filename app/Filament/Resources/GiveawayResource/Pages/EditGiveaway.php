<?php

namespace App\Filament\Resources\GiveawayResource\Pages;

use App\Filament\Resources\GiveawayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGiveaway extends EditRecord
{
    protected static string $resource = GiveawayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
