<?php

namespace App\Livewire\Components;

use App\Models\MonthlyPlayerAward;
use App\Models\PlayerReview;
use App\Support\Season;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FeaturedPlayers extends Component
{
    public function render()
    {
        $topPlayers = PlayerReview::with('player')
            ->select('player_id', DB::raw('AVG(rating) as avg_rating'))
            ->whereHas('player', fn ($q) => $q->where('is_coach', false))
            ->whereHas('match', fn ($q) => $q->where('season', Season::current()))
            ->groupBy('player_id')
            ->orderByDesc('avg_rating')
            ->take(4)
            ->get()
            ->filter(fn ($item) => $item->player)
            ->map(function ($item) {
                $player = $item->player;

                return [
                    'name' => $player->name,
                    'number' => $player->number,
                    'position' => $player->position,
                    'image' => $player->avatar_url ?? asset('images/default-player.png'),
                    'avg' => round($item->avg_rating, 2),
                ];
            });

        $playerOfMonthData = null;

        [$startIndex, $endIndex] = Season::monthIndexBounds(Season::current());

        $playerOfMonthAward = MonthlyPlayerAward::with('player')
            ->whereRaw('(year * 12 + month) >= ?', [$startIndex])
            ->whereRaw('(year * 12 + month) < ?', [$endIndex])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        if ($playerOfMonthAward && $playerOfMonthAward->player) {
            $player = $playerOfMonthAward->player;

            $playerOfMonthData = [
                'name' => $player->name,
                'number' => $player->number,
                'position' => $player->position,
                'image' => $player->avatar_url ?? asset('images/default-player.png'),
                'avg' => $playerOfMonthAward->average_rating,
            ];
        }

        return view('livewire.components.featured-players', [
            'players' => $topPlayers,
            'playerOfMonth' => $playerOfMonthData,
        ]);
    }
}
