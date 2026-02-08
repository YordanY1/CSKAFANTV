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
            ->values()
            ->take(5)
            ->toArray();

        $second = collect($service->getSecondLeagueStandings())
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
