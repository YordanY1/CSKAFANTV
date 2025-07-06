<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\FootballMatch;
use Illuminate\Support\Carbon;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $now = Carbon::now();

            $liveMatch = FootballMatch::where('match_datetime', '<=', $now)
                ->where('is_finished', false)
                ->whereNotNull('youtube_url')
                ->latest('match_datetime')
                ->first();

            $view->with('liveMatchYoutubeUrl', optional($liveMatch)->youtube_url);
        });
    }
}
