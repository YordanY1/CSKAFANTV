<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Player;
use App\Models\PlayerReview;
use App\Models\MonthlyPlayerAward;
use Illuminate\Support\Facades\DB;

class FeaturedPlayers extends Component
{
    public function render()
    {

        $topPlayers = PlayerReview::select('player_id', DB::raw('AVG(rating) as avg_rating'))
            ->whereHas('player', fn($q) => $q->where('is_coach', false))
            ->groupBy('player_id')
            ->orderByDesc('avg_rating')
            ->take(4)
            ->get()
            ->map(function ($item) {
                $player = Player::find($item->player_id);

                return [
                    'name'     => $player->name,
                    'number'   => $player->number,
                    'position' => $player->position,
                    'image'    => asset('storage/' . $player->image_path),
                    'avg'      => round($item->avg_rating, 2),
                ];
            });

        $playerOfMonthData = null;

        $playerOfMonthAward = MonthlyPlayerAward::with('player')
            ->where('year', now()->year)
            ->where('month', 12)
            ->first();

        if ($playerOfMonthAward && $playerOfMonthAward->player) {
            $player = $playerOfMonthAward->player;

            $playerOfMonthData = [
                'name'     => $player->name,
                'number'   => $player->number,
                'position' => $player->position,
                'image'    => asset('storage/' . $player->image_path),
                'avg'      => $playerOfMonthAward->average_rating,
            ];
        }

        return view('livewire.components.featured-players', [
            'players'       => $topPlayers,
            'playerOfMonth' => $playerOfMonthData,
        ]);
    }
}
