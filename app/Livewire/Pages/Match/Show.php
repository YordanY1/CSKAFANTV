<?php

namespace App\Livewire\Pages\Match;

use App\Models\FootballMatch;
use App\Models\PlayerReview;
use Livewire\Component;

class Show extends Component
{
    public FootballMatch $match;
    public array $ratings = [];

    public function mount(FootballMatch $match)
    {
        $this->match = $match->load([
            'homeTeam',
            'awayTeam',
            'lineup.player',
            'lineup.replacesPlayer',
        ]);
    }

    public function submitPlayerReviews(): void
    {
        $userId = auth()->id();

        foreach ($this->ratings as $playerId => $rating) {
            if ($rating && $rating >= 1 && $rating <= 5) {
                PlayerReview::updateOrCreate([
                    'user_id'   => $userId,
                    'player_id' => $playerId,
                    'match_id'  => $this->match->id,
                ], [
                    'rating'    => $rating,
                ]);
            }
        }

        session()->flash('message', 'Благодарим за оценките!');
        $this->reset('ratings');
    }

    public function render()
    {
        return view('livewire.pages.match.show')->layout('layouts.app')
            ->title("Match: " . $this->match->homeTeam->name . " vs " . $this->match->awayTeam->name);
    }
}
