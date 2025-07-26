<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use App\Models\Prediction;
use App\Models\PredictionResult;
use App\Models\User;

class RecalculatePredictionPoints extends Command
{
    protected $signature = 'predictions:recalculate';
    protected $description = 'Изчислява отново точките за всички прогнози и обновява класацията';

    public function handle()
    {
        $matches = FootballMatch::where('is_finished', true)
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->get();

        $updated = 0;

        foreach ($matches as $match) {
            $matchSign = match (true) {
                $match->home_score > $match->away_score => '1',
                $match->home_score < $match->away_score => '2',
                default => 'X',
            };

            $predictions = Prediction::where('football_match_id', $match->id)->get();

            foreach ($predictions as $prediction) {
                $points = 0;

                if (!is_null($prediction->home_score_prediction) && !is_null($prediction->away_score_prediction)) {
                    $exact = $prediction->home_score_prediction === $match->home_score
                        && $prediction->away_score_prediction === $match->away_score;

                    $predictedSign = match (true) {
                        $prediction->home_score_prediction > $prediction->away_score_prediction => '1',
                        $prediction->home_score_prediction < $prediction->away_score_prediction => '2',
                        default => 'X',
                    };

                    // Добавяме 1 т. за познат знак
                    if ($predictedSign === $matchSign) {
                        $points += 1;
                    }

                    // Добавяме 2 т. за точен резултат
                    if ($exact) {
                        $points += 2;
                    }
                }

                PredictionResult::updateOrCreate(
                    ['prediction_id' => $prediction->id],
                    [
                        'is_correct' => $points >= 2, // считаме за вярна ако е точен резултат
                        'points_awarded' => $points,
                    ]
                );

                $updated++;
            }
        }

        // Обновяване на total points при потребителите
        User::all()->each(function ($user) {
            $totalPoints = PredictionResult::whereIn('prediction_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('predictions')
                    ->where('user_id', $user->id);
            })->sum('points_awarded');

            $user->points = $totalPoints;
            $user->save();
        });

        $this->info("Обновени са {$updated} прогнози и всички потребители.");
    }
}
