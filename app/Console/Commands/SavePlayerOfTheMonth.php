<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PlayerReview;
use App\Models\MonthlyPlayerAward;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SavePlayerOfTheMonth extends Command
{
    protected $signature = 'player:save-monthly-award';
    protected $description = 'Calculate and store player of the month';

    public function handle(): void
    {
        $date = Carbon::create(2025, 12, 1);

        $monthStart = $date->startOfMonth();
        $monthEnd = $date->endOfMonth();

        $winner = PlayerReview::select('player_id', DB::raw('AVG(rating) as avg_rating'))
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereHas('player', function ($q) {
                $q->where('is_coach', 0);
            })
            ->groupBy('player_id')
            ->havingRaw('COUNT(*) >= 3')
            ->orderByDesc('avg_rating')
            ->first();


        if (!$winner) {
            $this->warn("⚠️ No player qualified for Player of the Month for {$monthStart->format('F Y')}.");
            return;
        }

        MonthlyPlayerAward::updateOrCreate(
            ['month' => 12, 'year' => 2025],
            [
                'player_id' => $winner->player_id,
                'average_rating' => round($winner->avg_rating, 2),
            ]
        );

        $this->info("✅ Player of the Month for December 2025 saved successfully.");
    }
}
