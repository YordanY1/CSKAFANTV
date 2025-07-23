<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class CookiePolicy extends Component
{
    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Политика за бисквитки | CSKA FAN TV',
            'description' => 'Разбери как и защо използваме бисквитки на сайта на CSKA FAN TV. Контролирай предпочитанията си за поверителност.',
            'robots' => 'noindex, nofollow',
            'canonical' => url('/cookie-policy'),
            'og_title' => 'Политика за бисквитки | CSKA FAN TV',
            'og_description' => 'Научи как използваме бисквитки и как те влияят на изживяването ти в платформата на червените фенове.',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/cookie-policy'),
            'og_type' => 'article',
        ];
    }

    public function render()
    {
        return view('livewire.pages.cookie-policy')
            ->layout('layouts.app', $this->layoutData);
    }
}
