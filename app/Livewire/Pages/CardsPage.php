<?php

namespace App\Livewire\Pages;

use App\Models\Card;
use Livewire\Component;

class CardsPage extends Component
{
    public function render()
    {
        $cards = Card::with('player')
            ->orderByRaw('(yellow_cards + red_cards + second_yellow_reds) DESC')
            ->get();

        return view('livewire.pages.cards-page', compact('cards'))->layout('layouts.app')
            ->title('Картони на всички играчи');
    }
}
