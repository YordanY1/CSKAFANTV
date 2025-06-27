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

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     return $data;
    // }

    // public function afterSave(): void
    // {

    //     $lineup = MatchLineup::where('football_match_id', $this->record->id)->get();

    //     $subs = $lineup->where('is_starter', false)->whereNotNull('replaces_player_id');


    //     foreach ($subs as $sub) {


    //         $replaced = $lineup->first(fn($p) => $p->player_id == $sub->replaces_player_id);

    //         if ($replaced) {
    //             $replaced->update([
    //                 'is_starter' => false,
    //                 'minute_substituted' => $sub->minute_entered,
    //             ]);

    //             $sub->update([
    //                 'is_starter' => true,
    //             ]);
    //         }
    //     }
    // }
}
