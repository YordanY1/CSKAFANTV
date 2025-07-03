<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use App\Models\Prediction;
use App\Models\PredictionResult;

class CalculatePredictionPoints extends Command
{
    protected $signature = 'predictions:calculate-points';
    protected $description = 'Изчислява точките за прогнози на завършили мачове';

    public function handle()
    {
        $finishedMatches = FootballMatch::where('is_finished', true)
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->get();

        foreach ($finishedMatches as $match) {
            $matchSign = match (true) {
                $match->home_score > $match->away_score => '1',
                $match->home_score < $match->away_score => '2',
                default => 'X',
            };

            $predictions = Prediction::where('football_match_id', $match->id)->get();

            foreach ($predictions as $prediction) {
                $points = 0;

                $exact = $prediction->home_score_prediction === $match->home_score
                    && $prediction->away_score_prediction === $match->away_score;

                if ($exact) {
                    $points = 3;
                } else {
                    $points = 0;
                }

                PredictionResult::updateOrCreate(
                    ['prediction_id' => $prediction->id],
                    [
                        'is_correct' => $points > 0,
                        'points_awarded' => $points,
                    ]
                );
            }
        }

        $this->info('Точките за прогнозите са изчислени.');
    }
}
