<?php

namespace App\Livewire\Pages;

use App\Models\PredictionResult;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PredictionRankingsPage extends Component
{
    public function render()
    {
        $rankings = PredictionResult::select('prediction_id', DB::raw('SUM(points_awarded) as total_points'), DB::raw('COUNT(*) as attempts'))
            ->groupBy('prediction_id')
            ->with('prediction.user')
            ->orderByDesc('total_points')
            ->get();

        return view('livewire.pages.prediction-rankings-page', [
            'rankings' => $rankings,
        ])->layout('layouts.app');
    }
}
