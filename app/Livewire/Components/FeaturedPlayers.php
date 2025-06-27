<?php

namespace App\Livewire\Components;

use App\Models\Player;
use App\Models\PlayerReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FeaturedPlayers extends Component
{
    public function render()
    {
        $topPlayers = PlayerReview::select('player_id', DB::raw('AVG(rating) as avg_rating'))
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
                    'image'    => asset($player->image_path),
                    'avg'      => round($item->avg_rating, 2),
                ];
            });

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $playerOfMonth = PlayerReview::select('player_id', DB::raw('AVG(rating) as avg_rating'))
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('player_id')
            ->orderByDesc('avg_rating')
            ->first();

        $playerOfMonthData = null;

        if ($playerOfMonth) {
            $player = Player::find($playerOfMonth->player_id);
            $playerOfMonthData = [
                'name'     => $player->name,
                'number'   => $player->number,
                'position' => $player->position,
                'image'    => asset($player->image_path),
                'avg'      => round($playerOfMonth->avg_rating, 2),
            ];
        }

        return view('livewire.components.featured-players', [
            'players' => $topPlayers,
            'playerOfMonth' => $playerOfMonthData,
        ]);
    }
}
