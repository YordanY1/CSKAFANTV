<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\Home;
use App\Livewire\Components\UpcomingMatches;
use App\Http\Controllers\Auth\SocialiteController;
use App\Livewire\Pages\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Livewire\Pages\Players;
use App\Livewire\Pages\Match\Show;
use App\Livewire\Pages\Videos;


Route::get('/', Home::class)->name('home');
Route::get('/matches/upcoming', UpcomingMatches::class)->name('matches.upcoming');
Route::get('/players', Players::class)->name('players');
Route::get('/match/{match}', Show::class)->name('match.show');


Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::get('/videos', Videos::class)->name('videos.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', UserProfile::class)->name('profile');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
