<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Prediction;
use App\Models\FootballMatch;
use Illuminate\Support\Facades\DB;

class PreviewPredictionPoints extends Command
{
    protected $signature = 'predictions:preview-points';
    protected $description = 'Преглежда точките за прогнози без да ги записва, според новото правило (2т точен + 1т знак)';

    public function handle()
    {
        $predictions = DB::table('predictions as p')
            ->join('football_matches as fm', 'p.football_match_id', '=', 'fm.id')
            ->join('users as u', 'p.user_id', '=', 'u.id')
            ->where('fm.is_finished', true)
            ->select('u.name', 'fm.home_score', 'fm.away_score', 'p.home_score_prediction', 'p.away_score_prediction')
            ->get();

        $this->info("Прогнози и пресметнати точки:");
        $this->line(str_pad('Потребител', 25) . str_pad('Реален', 10) . str_pad('Прогноза', 10) . 'Точки');

        foreach ($predictions as $p) {
            $points = 0;

            $matchSign = $this->getSign($p->home_score, $p->away_score);
            $predictedSign = $this->getSign($p->home_score_prediction, $p->away_score_prediction);

            if ($matchSign === $predictedSign) {
                $points += 1;
            }

            if ($p->home_score === $p->home_score_prediction && $p->away_score === $p->away_score_prediction) {
                $points += 2;
            }

            $this->line(
                str_pad($p->name, 25) .
                    str_pad("{$p->home_score}:{$p->away_score}", 10) .
                    str_pad("{$p->home_score_prediction}:{$p->away_score_prediction}", 10) .
                    "{$points}"
            );
        }
    }

    private function getSign(int $home, int $away): string
    {
        return match (true) {
            $home > $away => '1',
            $home < $away => '2',
            default => 'X',
        };
    }
}
