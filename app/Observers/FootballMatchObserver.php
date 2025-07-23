<?php

namespace App\Observers;

use App\Models\FootballMatch;
use Illuminate\Support\Facades\Cache;

class FootballMatchObserver
{
    public function updated(FootballMatch $match)
    {
        if (
            $match->isDirty('is_finished') ||
            $match->isDirty('youtube_url') ||
            $match->isDirty('match_datetime')
        ) {
            Cache::forget('live_match');
        }
    }
}
