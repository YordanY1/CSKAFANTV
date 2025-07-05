<?php

namespace App\Livewire\Pages;

use App\Models\Card;
use Livewire\Component;

class CardsPage extends Component
{
    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Картони – Играчите с най-много картони | CSKA FAN TV',
            'description' => 'Прегледай кои футболисти на ЦСКА са получили най-много жълти и червени картони през сезона. Детайлна статистика по играч.',
            'robots' => 'index, follow',
            'canonical' => url('/cards'),
            'og_title' => 'Най-много картони – Играчите на ЦСКА | CSKA FAN TV',
            'og_description' => 'Виж класация по картони – кои футболисти имат най-много нарушения и санкции. Информация от феновете за феновете.',
            'og_image' => asset('images/og-cska.jpg'),
            'og_url' => url('/cards'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        $cards = Card::with('player')
            ->orderByRaw('(yellow_cards + red_cards + second_yellow_reds) DESC')
            ->get();

        return view('livewire.pages.cards-page', compact('cards'))
            ->layout('layouts.app', $this->layoutData);
    }
}
