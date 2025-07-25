<?php

namespace App\Livewire\Components;

use App\Models\FootballMatch;
use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Models\Prediction;


class UpcomingMatches extends Component
{
    public string $filter = 'upcoming';

    protected $listeners = ['prediction-saved' => 'onPredictionSaved'];

    public array $predictedMatches = [];

    public function onPredictionSaved(int $matchId): void
    {
        $this->predictedMatches[] = $matchId;
    }


    public function mount()
    {
        $now = now();

        $hasLive = FootballMatch::whereBetween('match_datetime', [
            $now->copy()->subHours(2),
            $now->copy()->addMinutes(30),
        ])
            ->where('is_finished', false)
            ->exists();

        if ($hasLive) {
            $this->filter = 'live';
            return;
        }

        $hasRecentCompleted = FootballMatch::where('is_finished', true)
            ->whereBetween('match_datetime', [$now->copy()->subHours(48), $now])
            ->exists();

        if ($hasRecentCompleted) {
            $this->filter = 'completed';
            return;
        }

        $this->filter = 'upcoming';
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $now = Carbon::now();

        $matches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->when(
                $this->filter === 'live',
                fn($q) =>
                $q->whereBetween('match_datetime', [
                    $now->copy()->subHours(2),
                    $now->copy()->addMinutes(30),
                ])
                    ->where('is_finished', false)
            )
            ->when(
                $this->filter === 'upcoming',
                fn($q) =>
                $q->where('match_datetime', '>', $now)
                    ->where('is_finished', false)
            )
            ->when(
                $this->filter === 'completed',
                fn($q) =>
                $q->where('is_finished', true)
            )
            ->orderBy('match_datetime', $this->filter === 'completed' ? 'desc' : 'asc')
            ->get();

        $predictions = auth()->check()
            ? Prediction::where('user_id', auth()->id())
            ->whereIn('football_match_id', $matches->pluck('id'))
            ->get()
            ->keyBy('football_match_id')
            : collect();

        return view('livewire.components.upcoming-matches', [
            'matches' => $matches,
            'predictions' => $predictions,
        ])->layout('layouts.app');
    }
}
