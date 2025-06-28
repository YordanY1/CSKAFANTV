<?php

namespace App\Livewire\Pages;

use App\Models\Player;
use Livewire\Component;


class Players extends Component
{
    public function render()
    {
        return view('livewire.pages.players', [
            'players' => Player::with('team')->get(),
        ])->layout('layouts.app');
    }
}
