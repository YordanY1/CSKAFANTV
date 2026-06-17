<?php

namespace App\Livewire\Pages\Archive;

use App\Models\PlayerReview;
use App\Support\Season;
use Livewire\Component;

class ArchivePlayerRatings extends Component
{
    public string $season;

    public array $layoutData = [];

    public function mount(string $season): void
    {
        abort_unless(Season::isValid($season), 404);

        $this->season = $season;

        $this->layoutData = [
            'title' => 'Оценки на играчите – '.Season::label($season).' | Архив CSKA FAN TV',
            'description' => 'Средните оценки на играчите на ЦСКА според феновете за '.Season::label($season).'.',
            'robots' => 'index, follow',
            'canonical' => url('/archive/player-ratings/'.$season),
            'og_title' => 'Оценки на играчите – '.Season::label($season),
            'og_description' => 'Класация на играчите по средна оценка за '.Season::label($season).'.',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/archive/player-ratings/'.$season),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        $ratings = PlayerReview::query()
            ->whereHas('match', fn ($query) => $query->where('season', $this->season))
            ->with('player')
            ->selectRaw('player_id, AVG(rating) as avg_rating, COUNT(*) as votes')
            ->groupBy('player_id')
            ->orderByDesc('avg_rating')
            ->get()
            ->map(fn ($row) => [
                'player' => $row->player,
                'avg_rating' => round($row->avg_rating, 2),
                'votes' => $row->votes,
            ]);

        return view('livewire.pages.archive.player-ratings', [
            'ratings' => $ratings,
            'season' => $this->season,
            'seasons' => Season::all(),
        ])->layout('layouts.app', $this->layoutData);
    }
}
