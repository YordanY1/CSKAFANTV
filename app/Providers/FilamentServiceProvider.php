<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('viewFilament', function ($user) {
            return $user->email === 'cskafantv@gmail.com';
        });
    }
}
