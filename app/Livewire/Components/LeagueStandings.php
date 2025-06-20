<?php

namespace App\Livewire\Components;

use App\Models\Standing;
use Livewire\Component;

class LeagueStandings extends Component
{
    public function render()
    {
        $standings = Standing::with('team')
            ->orderByDesc('points')
            ->get();

        return view('livewire.components.league-standings', [
            'standings' => $standings,
        ]);
    }
}
