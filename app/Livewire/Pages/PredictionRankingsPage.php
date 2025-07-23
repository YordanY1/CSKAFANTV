<?php

namespace App\Livewire\Pages;

use App\Models\PredictionResult;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PredictionRankingsPage extends Component
{
    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Класиране по прогнози – Точки и Успеваемост | CSKA FAN TV',
            'description' => 'Виж най-добрите прогнози и потребители с най-много точки в нашата футболна класация. Сравни представянето си с другите фенове!',
            'robots' => 'index, follow',
            'canonical' => url('/prediction-rankings'),
            'og_title' => 'Футболни прогнози – Класиране и резултати | CSKA FAN TV',
            'og_description' => 'Класация на потребителите по успешни футболни прогнози. Води ли твоята прогноза? Разбери сега!',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/prediction-rankings'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        $rankings = DB::table('prediction_results')
            ->join('predictions', 'prediction_results.prediction_id', '=', 'predictions.id')
            ->join('users', 'predictions.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name',
                DB::raw('SUM(prediction_results.points_awarded) as total_points'),
                DB::raw('COUNT(prediction_results.id) as attempts')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_points')
            ->get();

        return view('livewire.pages.prediction-rankings-page', [
            'rankings' => $rankings,
        ])->layout('layouts.app', $this->layoutData);
    }
}
