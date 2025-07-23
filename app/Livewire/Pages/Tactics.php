<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Tactics extends Component
{
    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Футболна Тактическа Дъска | CSKA FAN TV',
            'description' => 'Създай свои футболни тактики, разположи играчи, добави стрелки и анализирай мачовете на ЦСКА с нашата интерактивна дъска.',
            'robots' => 'index, follow',
            'canonical' => url('/tactics'),
            'og_title' => 'Тактическа Дъска за Футболни Анализи | CSKA FAN TV',
            'og_description' => 'Интерактивна дъска за футболни фенове на ЦСКА – планирай тактики, анализирай мачове и създавай игрови ситуации.',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/tactics'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        return view('livewire.pages.tactics')
            ->layout('layouts.app', $this->layoutData);
    }
}
