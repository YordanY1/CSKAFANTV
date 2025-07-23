<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Models\FootballMatch;

class Navbar extends Component
{
    public ?string $liveMatchYoutubeUrl = null;

    public function mount()
    {
        $this->liveMatchYoutubeUrl = Cache::remember('live_match', now()->addSeconds(60), function () {
            $now = Carbon::now();

            return optional(
                FootballMatch::where('match_datetime', '<=', $now)
                    ->where('is_finished', false)
                    ->whereNotNull('youtube_url')
                    ->get()
                    ->filter(fn($match) => $now->lt($match->match_datetime->copy()->addMinutes($match->duration ?? 90)))
                    ->sortByDesc('match_datetime')
                    ->first()
            )->youtube_url;
        });
    }

    public function render()
    {
        return view('livewire.components.navbar');
    }
}
