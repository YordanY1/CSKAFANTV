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
        $isOBS = str_contains(request()->header('User-Agent'), 'OBS');

        return view('obs.match', compact('match', 'isOBS'));
    }


    public function json($slug)
    {
        $match = FootballMatch::where('slug', $slug)->firstOrFail();

        return response()->json([
            'home_score' => $match->obs_home_score ?? 0,
            'away_score' => $match->obs_away_score ?? 0,
            'started_at' => optional($match->started_at)->timestamp,
            'stopped_at' => optional($match->stopped_at)->timestamp,
            'adjust_seconds' => $match->adjust_seconds ?? 0,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
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

    public function updateScore($slug)
    {
        $match = FootballMatch::where('slug', $slug)->firstOrFail();

        $data = request()->validate([
            'team' => ['required', 'in:home,away'],
            'value' => ['required', 'integer', 'min:0', 'max:20']
        ]);

        if ($data['team'] === 'home') {
            $match->obs_home_score = $data['value'];
        } else {
            $match->obs_away_score = $data['value'];
        }

        $match->save();

        return response()->json(['success' => true]);
    }

    public function adjust($slug)
    {
        $match = FootballMatch::where('slug', $slug)->firstOrFail();

        $seconds = request()->validate([
            'seconds' => 'required|integer',
        ])['seconds'];

        $match->adjust_seconds += $seconds;
        $match->save();

        return response()->json([
            'adjust_seconds' => $match->adjust_seconds
        ]);
    }
}
