<?php

namespace App\Livewire\Components;

use App\Models\Card;
use Livewire\Component;

class TopDisciplinedPlayers extends Component
{
    public function render()
    {
        $mostCards = Card::with('player')
            ->orderByRaw('(yellow_cards + red_cards + second_yellow_reds) DESC')
            ->take(4)
            ->get();

        return view('livewire.components.top-disciplined-players', compact('mostCards'));
    }
}
