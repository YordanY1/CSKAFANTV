<?php

namespace App\Livewire\Components;

use Livewire\Component;

class CookieConsentBanner extends Component
{
    public bool $visible = false;

    public function mount() {}

    public function render()
    {
        return view('livewire.components.cookie-consent-banner');
    }
}
