<?php

namespace App\Livewire\Pages;

use App\Models\PlayerReview;
use Livewire\Component;

class PlayerRatingsPage extends Component
{
    protected $listeners = ['player-reviewed' => '$refresh'];

    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Оценки на играчите – Гласуване и класиране | CSKA FAN TV',
            'description' => 'Дай своята оценка и виж кои играчи на ЦСКА се представят най-добре според феновете. Средни оценки и брой гласове.',
            'robots' => 'index, follow',
            'canonical' => url('/player-ratings'),
            'og_title' => 'Играч на мача – Фен гласуване | CSKA FAN TV',
            'og_description' => 'Класация на най-добрите играчи на ЦСКА според феновете. Средни рейтинги, брой гласове и най-подкрепяни звезди.',
            'og_image' => asset('images/og-cska.jpg'),
            'og_url' => url('/player-ratings'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        $ratings = PlayerReview::with('player')
            ->selectRaw('player_id, AVG(rating) as avg_rating, COUNT(*) as votes')
            ->groupBy('player_id')
            ->orderByDesc('avg_rating')
            ->get()
            ->map(function ($row) {
                return [
                    'player'     => $row->player,
                    'avg_rating' => round($row->avg_rating, 2),
                    'votes'      => $row->votes,
                ];
            });

        return view('livewire.pages.player-ratings-page', compact('ratings'))
            ->layout('layouts.app', $this->layoutData);
    }
}
