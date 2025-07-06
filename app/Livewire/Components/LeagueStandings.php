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
            ->sort(function ($a, $b) {
                $pointsA = $a->calculated_points;
                $pointsB = $b->calculated_points;

                if ($pointsA !== $pointsB) {
                    return $pointsB <=> $pointsA;
                }

                $gdA = $a->goal_difference;
                $gdB = $b->goal_difference;

                if ($gdA !== $gdB) {
                    return $gdB <=> $gdA;
                }

                if ($a->goals_scored !== $b->goals_scored) {
                    return $b->goals_scored <=> $a->goals_scored;
                }

                return strcmp(strtolower($a->team?->name), strtolower($b->team?->name));
            })
            ->take(5)
            ->values();

        return view('livewire.components.league-standings', [
            'standings' => $standings,
        ]);
    }
}
