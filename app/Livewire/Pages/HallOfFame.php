<?php

namespace App\Livewire\Pages;

use App\Models\MonthlyPlayerAward;
use App\Support\Season;
use Livewire\Component;

class HallOfFame extends Component
{
    public array $layoutData = [];

    public function mount(): void
    {
        $this->layoutData = [
            'title' => 'Зала на славата | Играчите на месеца – CSKA FAN TV',
            'description' => 'Виж всички играчи на месеца, подредени по месеци. Картички със средна оценка, позиция и визия на героите на сезона.',
            'robots' => 'index, follow',
            'canonical' => url('/hall-of-fame'),
            'og_title' => 'Зала на славата – Играчите на месеца',
            'og_description' => 'Футболна слава в пълния ѝ блясък – виж всички звезди на CSKA FAN TV избрани за играчи на месеца.',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/hall-of-fame'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        [$startIndex, $endIndex] = Season::monthIndexBounds(Season::current());

        $awards = MonthlyPlayerAward::with('player')
            ->whereRaw('(year * 12 + month) >= ?', [$startIndex])
            ->whereRaw('(year * 12 + month) < ?', [$endIndex])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('livewire.pages.hall-of-fame', [
            'awards' => $awards,
        ])->layout('layouts.app', $this->layoutData);
    }
}
