<?php

namespace App\Filament\Resources\GiveawayDrawResource\Pages;

use App\Filament\Resources\GiveawayDrawResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGiveawayDraw extends EditRecord
{
    protected static string $resource = GiveawayDrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
