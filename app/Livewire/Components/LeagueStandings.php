<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\LiveScoreService;

class LeagueStandings extends Component
{
    public array $firstLeague = [];
    public array $secondLeague = [];

    public string $league = 'first';

    public function mount(LiveScoreService $service): void
    {
        $first = collect($service->getStandingsWithTeams(71))
            ->sort(function ($a, $b) {
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
            })
            ->values()
            ->take(5)
            ->toArray();

        $second = collect($service->getSecondLeagueStandings())
            ->sort(function ($a, $b) {
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
            })
            ->values()
            ->take(5)
            ->toArray();

        $this->firstLeague = $first;
        $this->secondLeague = $second;
    }

    public function getStandingsProperty(): array
    {
        return $this->league === 'first'
            ? $this->firstLeague
            : $this->secondLeague;
    }

    public function render()
    {
        return view('livewire.components.league-standings', [
            'standings' => $this->standings,
            'league' => $this->league,
        ]);
    }
}
