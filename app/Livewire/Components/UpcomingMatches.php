<?php

namespace App\Livewire\Components;

use App\Models\FootballMatch;
use Livewire\Component;

class UpcomingMatches extends Component
{
    public function render()
    {
        $matches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->where('is_finished', 0)
            ->where('match_datetime', '>', now())
            ->orderBy('match_datetime')
            ->take(6)
            ->get();


        return view('livewire.components.upcoming-matches', [
            'matches' => $matches,
        ])->layout('layouts.app');
    }
}
