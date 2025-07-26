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
        $match = FootballMatch::select('home_score', 'away_score')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'home_score' => $match->home_score,
            'away_score' => $match->away_score,
        ]);
    }
}
