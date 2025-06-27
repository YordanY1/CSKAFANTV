<?php

namespace App\Livewire\Components;

use App\Models\FootballMatch;
use Livewire\Component;
use Illuminate\Support\Carbon;

class UpcomingMatches extends Component
{
    public string $filter = 'upcoming';

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $now = Carbon::now();

        $matches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->when($this->filter === 'upcoming', fn($q) => $q->where('match_datetime', '>', $now))
            ->when($this->filter === 'completed', fn($q) => $q->where('match_datetime', '<=', $now))
            ->orderBy('match_datetime', $this->filter === 'upcoming' ? 'asc' : 'desc')
            ->get();

        return view('livewire.components.upcoming-matches', [
            'matches' => $matches,
        ])->layout('layouts.app');
    }
}
