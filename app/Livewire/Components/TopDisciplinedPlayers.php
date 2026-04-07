<?php

namespace App\Livewire\Components;

use App\Models\Card;
use Livewire\Component;

class TopDisciplinedPlayers extends Component
{
    public function render()
    {
        $mostCards = Card::with('player')
            ->orderByRaw('(yellow_cards + (CASE WHEN direct_red_note IS NOT NULL AND direct_red_note != \'\' THEN 1 ELSE 0 END) + second_yellow_reds) DESC')
            ->take(4)
            ->get();

        return view('livewire.components.top-disciplined-players', compact('mostCards'));
    }
}
