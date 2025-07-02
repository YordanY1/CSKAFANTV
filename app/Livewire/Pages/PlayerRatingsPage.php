<?php

namespace App\Livewire\Pages;

use App\Models\Player;
use App\Models\PlayerReview;
use Livewire\Component;

class PlayerRatingsPage extends Component
{
    public function render()
    {
        $ratings = PlayerReview::with('player')
            ->selectRaw('player_id, AVG(rating) as avg_rating, COUNT(*) as votes')
            ->groupBy('player_id')
            ->orderByDesc('avg_rating')
            ->get()
            ->map(function ($row) {
                return [
                    'player'     => $row->player,
                    'avg_rating' => round($row->avg_rating, 2),
                    'votes'      => $row->votes,
                ];
            });

        return view('livewire.pages.player-ratings-page', compact('ratings'))
            ->layout('layouts.app');
    }
}
