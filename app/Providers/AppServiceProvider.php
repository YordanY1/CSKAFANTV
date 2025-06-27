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

            $liveMatch = FootballMatch::whereBetween('match_datetime', [
                $now->copy()->subHours(2),
                $now->copy()->addMinutes(30),
            ])->whereNotNull('youtube_url')->latest('match_datetime')->first();

            $view->with('liveMatchYoutubeUrl', optional($liveMatch)->youtube_url);
        });
    }
}
