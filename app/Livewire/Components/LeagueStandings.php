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
            ->sortByDesc(function ($standing) {
                return [
                    ($standing->wins * 3) + $standing->draws,
                    $standing->goals_scored - $standing->goals_conceded,
                    $standing->team?->name
                ];
            })
            ->take(5)
            ->values();

        return view('livewire.components.league-standings', [
            'standings' => $standings,
        ]);
    }
}
