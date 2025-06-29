<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class PrivacyPolicy extends Component
{
    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Политика за поверителност | CSKA FAN TV',
            'description' => 'Научи как обработваме и защитаваме личните ти данни като потребител на CSKA FAN TV – подкастът на червените фенове.',
            'robots' => 'noindex, nofollow',
            'canonical' => url('/privacy-policy'),
            'og_title' => 'Политика за поверителност | CSKA FAN TV',
            'og_description' => 'Прочети как съхраняваме, използваме и защитаваме личната информация в платформата на CSKA FAN TV.',
            'og_image' => asset('images/og-cska.jpg'),
            'og_url' => url('/privacy-policy'),
            'og_type' => 'article',
        ];
    }

    public function render()
    {
        return view('livewire.pages.privacy-policy')
            ->layout('layouts.app', $this->layoutData);
    }
}
