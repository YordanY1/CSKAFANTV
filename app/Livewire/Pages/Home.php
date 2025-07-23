<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Home extends Component
{
    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'CSKA FAN TV – Подкаст, интервюта и всичко за ЦСКА',
            'description' => 'Гледай подкасти, интервюта и коментари за любимия ти отбор. Само за червени фенове!',
            'robots' => 'index, follow',
            'canonical' => url('/'),
            'og_title' => 'CSKA FAN TV – Официалният фен подкаст на червените',
            'og_description' => 'Интервюта с легенди, анализи, томболи и още – всичко за ЦСКА на едно място!',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        return view('livewire.pages.home')
            ->layout('layouts.app', $this->layoutData);
    }
}
