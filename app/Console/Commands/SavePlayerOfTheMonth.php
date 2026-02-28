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
    protected $description = 'Calculate and store Player of the Month for the previous month';

    public function handle(): int
    {
        $targetDate = Carbon::now('Europe/Sofia')->subMonth();

        $year = $targetDate->year;
        $month = $targetDate->month;

        if (MonthlyPlayerAward::where('year', $year)
            ->where('month', $month)
            ->exists()
        ) {
            $this->warn("⚠️ Player of the Month for {$targetDate->format('F Y')} is already calculated.");
            return Command::SUCCESS;
        }

        $monthStart = $targetDate->copy()->startOfMonth();
        $monthEnd = $targetDate->copy()->endOfMonth();

        $winner = PlayerReview::select(
            'player_id',
            DB::raw('AVG(rating) as avg_rating'),
            DB::raw('COUNT(*) as reviews_count')
        )
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereHas('player', function ($query) {
                $query->where('is_coach', false);
            })
            ->groupBy('player_id')
            ->havingRaw('COUNT(*) >= 3')
            ->orderByDesc('avg_rating')
            ->orderByDesc('reviews_count')
            ->first();

        if (!$winner) {
            $this->warn("⚠️ No player qualified for {$targetDate->format('F Y')}.");
            return Command::SUCCESS;
        }

        MonthlyPlayerAward::create([
            'player_id'      => $winner->player_id,
            'month'          => $month,
            'year'           => $year,
            'average_rating' => round($winner->avg_rating, 2),
        ]);

        $this->info("✅ Player of the Month for {$targetDate->format('F Y')} saved successfully.");

        return Command::SUCCESS;
    }
}
