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
            'og_image' => asset('images/og-cska.jpg'),
            'og_url' => url('/prediction-rankings'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        $rankings = PredictionResult::select('prediction_id', DB::raw('SUM(points_awarded) as total_points'), DB::raw('COUNT(*) as attempts'))
            ->groupBy('prediction_id')
            ->with('prediction.user')
            ->orderByDesc('total_points')
            ->get();

        return view('livewire.pages.prediction-rankings-page', [
            'rankings' => $rankings,
        ])->layout('layouts.app', $this->layoutData);
    }
}
