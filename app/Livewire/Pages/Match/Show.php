<?php

namespace App\Livewire\Pages\Match;

use App\Models\FootballMatch;
use App\Models\PlayerReview;
use Livewire\Component;

class Show extends Component
{
    public FootballMatch $match;
    public array $ratings = [];

    public array $layoutData = [];

    public function mount(FootballMatch $match)
    {
        $this->match = $match->load([
            'homeTeam',
            'awayTeam',
            'lineup.player',
            'lineup.replacesPlayer',
        ]);

        $home = $this->match->homeTeam->name;
        $away = $this->match->awayTeam->name;
        $date = $this->match->match_date?->format('d.m.Y') ?? '';


        $this->layoutData = [
            'title' => "$home vs $away – $date | CSKA FAN TV",
            'description' => "Преглед на мача между $home и $away, проведен на $date. Виж съставите, оцени играчите и сподели мнение.",
            'robots' => 'index, follow',
            'canonical' => url()->current(),
            'og_title' => "$home срещу $away – $date | CSKA FAN TV",
            'og_description' => "Фенски анализ на двубоя $home срещу $away. Оцени футболистите и виж какво мислят другите фенове на ЦСКА.",
            'og_image' => asset('images/og-cska.jpg'),
            'og_url' => url()->current(),
            'og_type' => 'article',
        ];
    }

    public function submitPlayerReviews(): void
    {
        $userId = auth()->id();

        foreach ($this->ratings as $playerId => $rating) {
            if ($rating && $rating >= 1 && $rating <= 5) {
                PlayerReview::updateOrCreate([
                    'user_id'   => $userId,
                    'player_id' => $playerId,
                    'match_id'  => $this->match->id,
                ], [
                    'rating'    => $rating,
                ]);
            }
        }

        session()->flash('message', 'Благодарим за оценките!');
        $this->reset('ratings');
    }

    public function render()
    {
        return view('livewire.pages.match.show')
            ->layout('layouts.app', $this->layoutData);
    }
}
