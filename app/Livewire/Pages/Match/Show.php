<?php

namespace App\Livewire\Pages\Match;

use App\Models\FootballMatch;
use App\Models\PlayerReview;
use Livewire\Component;
use App\Models\Player;

class Show extends Component
{
    public FootballMatch $match;
    public array $ratings = [];
    public array $existingReviews = [];
    public array $averageRatings = [];
    public array $layoutData = [];
    public $coach;

    protected $listeners = ['player-reviewed' => 'refreshAverageRatings'];

    public function mount(FootballMatch $match)
    {
        $this->match = $match->load([
            'homeTeam',
            'awayTeam',
            'lineup.player',
            'lineup.replacesPlayer',
        ]);

        $this->coach = Player::where('is_coach', true)->first();

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
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url()->current(),
            'og_type' => 'article',
        ];

        $this->existingReviews = PlayerReview::where('user_id', auth()->id())
            ->where('match_id', $this->match->id)
            ->pluck('rating', 'player_id')
            ->toArray();

        $this->loadAverageRatings();
    }

    public function refreshExistingReviews(): void
    {
        $this->existingReviews = PlayerReview::where('user_id', auth()->id())
            ->where('match_id', $this->match->id)
            ->pluck('rating', 'player_id')
            ->toArray();
    }


    public function submitPlayerReviews(): void
    {
        $userId = auth()->id();

        foreach ($this->ratings as $playerId => $rating) {
            if ($rating && $rating >= 1 && $rating <= 10) {
                $alreadyRated = PlayerReview::where('user_id', $userId)
                    ->where('player_id', $playerId)
                    ->where('match_id', $this->match->id)
                    ->exists();

                if (!$alreadyRated) {
                    PlayerReview::create([
                        'user_id'   => $userId,
                        'player_id' => $playerId,
                        'match_id'  => $this->match->id,
                        'rating'    => $rating,
                    ]);
                }
            }
        }

        session()->flash('message', 'Благодарим за оценките!');
        $this->reset('ratings');

        $this->refreshExistingReviews();

        $this->dispatch('player-reviewed');
    }


    public function refreshAverageRatings(): void
    {
        $this->loadAverageRatings();
    }

    private function loadAverageRatings(): void
    {
        $this->averageRatings = PlayerReview::where('match_id', $this->match->id)
            ->selectRaw('player_id, ROUND(AVG(rating), 2) as average_rating')
            ->groupBy('player_id')
            ->pluck('average_rating', 'player_id')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.pages.match.show', [
            'coach' => $this->coach,
            'averageRatings' => $this->averageRatings,
            'existingReviews' => $this->existingReviews,
        ])->layout('layouts.app', $this->layoutData);
    }
}
