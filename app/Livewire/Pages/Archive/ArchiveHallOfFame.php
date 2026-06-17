<?php

namespace App\Livewire\Pages\Archive;

use App\Models\MonthlyPlayerAward;
use App\Support\Season;
use Livewire\Component;

class ArchiveHallOfFame extends Component
{
    public string $season;

    public array $layoutData = [];

    public function mount(string $season): void
    {
        abort_unless(Season::isValid($season), 404);

        $this->season = $season;

        $this->layoutData = [
            'title' => 'Зала на славата – '.Season::label($season).' | Архив CSKA FAN TV',
            'description' => 'Играчите на месеца на ЦСКА за '.Season::label($season).'.',
            'robots' => 'index, follow',
            'canonical' => url('/archive/hall-of-fame/'.$season),
            'og_title' => 'Зала на славата – '.Season::label($season),
            'og_description' => 'Всички играчи на месеца за '.Season::label($season).'.',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/archive/hall-of-fame/'.$season),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        [$startIndex, $endIndex] = Season::monthIndexBounds($this->season);

        $awards = MonthlyPlayerAward::with('player')
            ->whereRaw('(year * 12 + month) >= ?', [$startIndex])
            ->whereRaw('(year * 12 + month) < ?', [$endIndex])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('livewire.pages.archive.hall-of-fame', [
            'awards' => $awards,
            'season' => $this->season,
            'seasons' => Season::all(),
        ])->layout('layouts.app', $this->layoutData);
    }
}
