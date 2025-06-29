<?php

namespace App\Livewire\Pages;

use App\Models\Player;
use Livewire\Component;

class Players extends Component
{
    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Играчите на ЦСКА – Състав, номера и позиции | CSKA FAN TV',
            'description' => 'Разгледай актуалния състав на ЦСКА: играчи, позиции, номера и отбори. Информация за футболистите в подкаста на феновете.',
            'robots' => 'index, follow',
            'canonical' => url('/players'),
            'og_title' => 'Състав на ЦСКА – Всички играчи | CSKA FAN TV',
            'og_description' => 'Официалният списък на футболистите на ЦСКА с номера, позиции и отборна принадлежност.',
            'og_image' => asset('images/og-cska.jpg'),
            'og_url' => url('/players'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        return view('livewire.pages.players', [
            'players' => Player::with('team')->get(),
        ])->layout('layouts.app', $this->layoutData);
    }
}
