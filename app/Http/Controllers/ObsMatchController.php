<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;

class ObsMatchController extends Controller
{
    public function show($slug)
    {
        $match = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->where('slug', $slug)
            ->firstOrFail();

        $expectedToken = hash_hmac('sha256', $match->slug, config('app.key'));

        if (request('token') !== $expectedToken) {
            abort(403, 'Нямаш достъп до OBS изгледа.');
        }

        return view('obs.match', compact('match'));
    }

    public function json($slug)
    {
        $match = FootballMatch::where('slug', $slug)->firstOrFail();

        return response()->json([
            'home_score' => $match->home_score,
            'away_score' => $match->away_score,
            'started_at' => optional($match->started_at)->timestamp,
            'stopped_at' => optional($match->stopped_at)->timestamp,
        ]);
    }



    public function stop($slug)
    {
        $match = FootballMatch::where('slug', $slug)->firstOrFail();
        $match->stopped_at = now();
        $match->save();

        return response()->json(['stopped_at' => $match->stopped_at]);
    }

    public function start($slug)
    {
        $match = FootballMatch::where('slug', $slug)->firstOrFail();
        $match->started_at = now();
        $match->stopped_at = null;
        $match->save();

        \Log::info('🎯 Започна мачът', [
            'slug' => $slug,
            'started_at' => $match->started_at,
        ]);

        return response()->json(['started_at' => $match->started_at]);
    }


    public function resume($slug)
    {
        $match = FootballMatch::where('slug', $slug)->firstOrFail();

        if ($match->started_at && $match->stopped_at) {
            $pausedDuration = $match->stopped_at->diffInSeconds(now());
            $match->started_at = $match->started_at->addSeconds($pausedDuration);
        }

        $match->stopped_at = null;
        $match->save();

        return response()->json(['resumed' => true]);
    }


    public function reset($slug)
    {
        $match = FootballMatch::where('slug', $slug)->firstOrFail();
        $match->started_at = null;
        $match->stopped_at = null;
        $match->save();

        return response()->json(['reset' => true]);
    }
}
