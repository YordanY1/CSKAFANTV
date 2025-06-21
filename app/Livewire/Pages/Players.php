<?php

namespace App\Livewire\Pages;

use App\Models\Player;
use Livewire\Component;
use Livewire\WithPagination;

class Players extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.pages.players', [
            'players' => Player::with('team')->paginate(12),
        ])->layout('layouts.app');
    }
}
