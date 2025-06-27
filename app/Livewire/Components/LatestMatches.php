<?php

namespace App\Livewire\Components;

use App\Models\FootballMatch;
use Illuminate\Support\Carbon;
use Livewire\Component;

class LatestMatches extends Component
{
    public string $filter = 'upcoming';

    public function setFilter(string $type)
    {
        $this->filter = $type;
    }

    public function render()
    {
        $now = Carbon::now();

        $matches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->when($this->filter === 'upcoming', fn($q) => $q->where('match_datetime', '>', $now))
            ->when($this->filter === 'completed', fn($q) => $q->where('match_datetime', '<=', $now))
            ->orderBy('match_datetime', $this->filter === 'upcoming' ? 'asc' : 'desc')
            ->get();

        return view('livewire.components.latest-matches', [
            'matches' => $matches,
        ]);
    }
}
