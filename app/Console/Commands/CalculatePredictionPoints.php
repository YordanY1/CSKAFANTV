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

                $hasPrediction = !is_null($prediction->home_score_prediction) && !is_null($prediction->away_score_prediction);

                if ($hasPrediction) {
                    $predictionSign = match (true) {
                        $prediction->home_score_prediction > $prediction->away_score_prediction => '1',
                        $prediction->home_score_prediction < $prediction->away_score_prediction => '2',
                        default => 'X',
                    };

                    if ($predictionSign === $matchSign) {
                        $points += 1;
                    }

                    if (
                        $prediction->home_score_prediction === $match->home_score &&
                        $prediction->away_score_prediction === $match->away_score
                    ) {
                        $points += 2;
                    }
                }

                PredictionResult::updateOrCreate(
                    ['prediction_id' => $prediction->id],
                    [
                        'is_correct' => $points === 3,
                        'points_awarded' => $points,
                    ]
                );
            }
        }

        $this->info('Точките за прогнозите са изчислени по новата логика (2т + 1т).');
    }
}
