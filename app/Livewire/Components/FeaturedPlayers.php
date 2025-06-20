<?php

namespace App\Livewire\Components;

use Livewire\Component;

class FeaturedPlayers extends Component
{
    public function render()
    {
        $players = [
            ['name' => 'Lionel Messi', 'number' => 10, 'position' => 'Forward', 'image' => '/images/players/messi.png'],
            ['name' => 'Cristiano Ronaldo', 'number' => 7, 'position' => 'Forward', 'image' => '/images/players/ronaldo.png'],
            ['name' => 'Tibo Vion', 'number' => 7, 'position' => 'Defender', 'image' => '/images/players/vion.png'],
            ['name' => 'Harry Kane', 'number' => 9, 'position' => 'Striker', 'image' => '/images/players/kane.png'],
        ];

        return view('livewire.components.featured-players', compact('players'));
    }
}
