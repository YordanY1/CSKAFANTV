<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\PlayerReview;
use App\Models\MonthlyPlayerAward;
use Illuminate\Support\Facades\DB;

class FeaturedPlayers extends Component
{
    public function render()
    {
        $topPlayers = PlayerReview::with('player')
            ->select('player_id', DB::raw('AVG(rating) as avg_rating'))
            ->whereHas('player', fn($q) => $q->where('is_coach', false))
            ->groupBy('player_id')
            ->orderByDesc('avg_rating')
            ->take(4)
            ->get()
            ->filter(fn($item) => $item->player)
            ->map(function ($item) {
                $player = $item->player;

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
            ->orderByDesc('year')
            ->orderByDesc('month')
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
