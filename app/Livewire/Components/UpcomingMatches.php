<?php

namespace App\Livewire\Components;

use App\Models\FootballMatch;
use Livewire\Component;
use Illuminate\Support\Carbon;

class UpcomingMatches extends Component
{
    public string $filter = 'upcoming';

    public function mount()
    {
        $now = now();
        $hasLive = FootballMatch::where('match_datetime', '<=', $now)
            ->where('match_datetime', '>=', $now->copy()->subHours(2))
            ->exists();

        if ($hasLive) {
            $this->filter = 'live';
        }
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $now = Carbon::now();

        $matches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->when(
                $this->filter === 'live',
                fn($q) =>
                $q->whereBetween('match_datetime', [
                    $now->copy()->subHours(2),
                    $now->copy()->addMinutes(30)
                ])
            )
            ->when(
                $this->filter === 'upcoming',
                fn($q) =>
                $q->where('match_datetime', '>', $now)
            )
            ->when(
                $this->filter === 'completed',
                fn($q) =>
                $q->where('match_datetime', '<=', $now->copy()->subHours(2))
            )
            ->orderBy('match_datetime', 'asc')
            ->get();

        return view('livewire.components.upcoming-matches', [
            'matches' => $matches,
        ])->layout('layouts.app');
    }
}
