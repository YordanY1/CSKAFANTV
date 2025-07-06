<?php

namespace App\Livewire\Components;

use App\Models\Standing;
use Livewire\Component;

class LeagueStandings extends Component
{
    public function render()
    {
        $standings = Standing::with('team')
            ->get()
            ->sortBy(function ($standing) {
                return $standing->manual_rank ?? 1000;
            })
            ->take(5)
            ->values();

        return view('livewire.components.league-standings', [
            'standings' => $standings,
        ]);
    }
}
