<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\LiveScoreService;

class LeagueStandings extends Component
{
    public function render(LiveScoreService $service)
    {
        $all = collect($service->getStandingsWithTeams(71));

        $sorted = $all->sort(function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] <=> $a['points'];
            }

            if ($a['goal_diff'] !== $b['goal_diff']) {
                return $b['goal_diff'] <=> $a['goal_diff'];
            }

            if ($a['goals_scored'] !== $b['goals_scored']) {
                return $b['goals_scored'] <=> $a['goals_scored'];
            }

            return strcmp(mb_strtolower($a['name']), mb_strtolower($b['name']));
        })->values();

        $standings = $sorted->take(5);

        return view('livewire.components.league-standings', [
            'standings' => $standings,
        ]);
    }
}
