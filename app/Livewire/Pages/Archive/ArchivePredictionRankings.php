<?php

namespace App\Livewire\Pages\Archive;

use App\Support\Season;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ArchivePredictionRankings extends Component
{
    public string $season;

    public array $layoutData = [];

    public function mount(string $season): void
    {
        abort_unless(Season::isValid($season), 404);

        $this->season = $season;

        $this->layoutData = [
            'title' => 'Класиране по прогнози – '.Season::label($season).' | Архив CSKA FAN TV',
            'description' => 'Класацията на потребителите по точки от прогнози за '.Season::label($season).'.',
            'robots' => 'index, follow',
            'canonical' => url('/archive/prediction-rankings/'.$season),
            'og_title' => 'Класиране по прогнози – '.Season::label($season),
            'og_description' => 'Кой събра най-много точки от прогнози през '.Season::label($season).'?',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/archive/prediction-rankings/'.$season),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        $rankings = DB::table('prediction_results')
            ->join('predictions', 'prediction_results.prediction_id', '=', 'predictions.id')
            ->join('users', 'predictions.user_id', '=', 'users.id')
            ->join('football_matches', 'predictions.football_match_id', '=', 'football_matches.id')
            ->where('football_matches.season', $this->season)
            ->select(
                'users.id as user_id',
                'users.name',
                DB::raw('SUM(prediction_results.points_awarded) as total_points'),
                DB::raw('COUNT(DISTINCT predictions.football_match_id) as attempts')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_points')
            ->get();

        return view('livewire.pages.archive.prediction-rankings', [
            'rankings' => $rankings,
            'season' => $this->season,
            'seasons' => Season::all(),
        ])->layout('layouts.app', $this->layoutData);
    }
}
