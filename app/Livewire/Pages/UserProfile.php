<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class UserProfile extends Component
{
    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Моят Профил | CSKA FAN TV',
            'description' => 'Управлявай своя потребителски профил, настройки и участие в игри и томболи. Стани част от червената общност!',
            'robots' => 'noindex, nofollow',
            'canonical' => url('/user-profile'),
            'og_title' => 'Потребителски Профил | CSKA FAN TV',
            'og_description' => 'Достъп до личната ти информация, предпочитания и участие в кампании на CSKA FAN TV.',
            'og_image' => asset('images/og-cska.jpg'),
            'og_url' => url('/user-profile'),
            'og_type' => 'profile',
        ];
    }

    public function render()
    {
        return view('livewire.pages.user-profile')
            ->layout('layouts.app', $this->layoutData);
    }
}
