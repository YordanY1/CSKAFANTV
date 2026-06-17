<?php

namespace App\Livewire\Pages\Archive;

use App\Models\FootballMatch;
use App\Support\Season;
use Livewire\Component;

class ArchiveMatches extends Component
{
    public string $season;

    public array $layoutData = [];

    public function mount(string $season): void
    {
        abort_unless(Season::isValid($season), 404);

        $this->season = $season;

        $this->layoutData = [
            'title' => 'Мачове – '.Season::label($season).' | Архив CSKA FAN TV',
            'description' => 'Всички изиграни мачове на ЦСКА от '.Season::label($season).' с крайни резултати.',
            'robots' => 'index, follow',
            'canonical' => url('/archive/matches/'.$season),
            'og_title' => 'Мачове – '.Season::label($season),
            'og_description' => 'Архив на изиграните мачове от '.Season::label($season).'.',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/archive/matches/'.$season),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        $matches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->forSeason($this->season)
            ->where('is_finished', true)
            ->orderByDesc('match_datetime')
            ->get();

        return view('livewire.pages.archive.matches', [
            'matches' => $matches,
            'season' => $this->season,
            'seasons' => Season::all(),
        ])->layout('layouts.app', $this->layoutData);
    }
}
