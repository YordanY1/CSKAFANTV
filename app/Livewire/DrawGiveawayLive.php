<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\GiveawayDraw;
use Illuminate\Support\Facades\Log;

class DrawGiveawayLive extends Component
{
    public $lastWinner = null;
    public $loading = false;

    public function draw()
    {
        $this->loading = true;

        sleep(2);

        $winner = User::inRandomOrder()->first();

        if (!$winner) {
            $this->dispatch('notify', title: 'Няма потребители!', type: 'danger');
            $this->loading = false;
            return;
        }

        GiveawayDraw::create([
            'user_id' => $winner->id,
        ]);

        $this->lastWinner = $winner;
        $this->loading = false;

        $this->dispatch('notify', title: "🎉 Победител: {$winner->name}", type: 'success');
    }

    public function render()
    {
        return view('livewire.draw-giveaway-live');
    }
}
