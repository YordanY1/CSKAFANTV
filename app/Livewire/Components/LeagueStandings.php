<?php

namespace App\Livewire\Components;

use App\Models\Standing;
use Livewire\Component;

class LeagueStandings extends Component
{
    public function render()
    {

        $all = Standing::with('team')->get();

        $auto = $all->filter(fn($s) => $s->manual_rank === null);

        $sorted = $auto->sort(function ($a, $b) {
            if ($a->calculated_points !== $b->calculated_points) {
                return $b->calculated_points <=> $a->calculated_points;
            }

            if ($a->goal_difference !== $b->goal_difference) {
                return $b->goal_difference <=> $a->goal_difference;
            }

            if ($a->goals_scored !== $b->goals_scored) {
                return $b->goals_scored <=> $a->goals_scored;
            }

            return strcmp(strtolower($a->team?->name), strtolower($b->team?->name));
        })->values();


        $final = collect();
        $manuals = $all->filter(fn($s) => $s->manual_rank !== null);

        foreach ($manuals as $m) {
            $final->put($m->manual_rank - 1, $m);
        }

        $i = 0;
        foreach ($sorted as $team) {
            while ($final->has($i)) {
                $i++;
            }
            $final->put($i, $team);
            $i++;
        }

        $standings = $final->sortKeys()->take(5)->values();

        return view('livewire.components.league-standings', [
            'standings' => $standings,
        ]);
    }
}
