<?php

namespace App\Filament\Resources\PlayerRatingResource\Pages;

use App\Filament\Resources\PlayerRatingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerRating extends EditRecord
{
    protected static string $resource = PlayerRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
