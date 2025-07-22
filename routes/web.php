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
use App\Livewire\Pages\Tactics;
use App\Livewire\Pages\Contact;
use App\Livewire\Pages\CardsPage;
use App\Livewire\Pages\PrivacyPolicy;
use App\Livewire\Pages\CookiePolicy;
use App\Livewire\Pages\FullStandingsPage;
use App\Livewire\Pages\PlayerRatingsPage;
use App\Livewire\Pages\PredictionRankingsPage;



Route::get('/', Home::class)->name('home');
Route::get('/matches', UpcomingMatches::class)->name('matches');
Route::get('/players', Players::class)->name('players');
Route::get('/match/{match:slug}', Show::class)->name('match.show');



Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::get('/auth/facebook/redirect', [SocialiteController::class, 'redirectToFacebook'])->name('auth.facebook.redirect');
Route::get('/auth/facebook/callback', [SocialiteController::class, 'handleFacebookCallback']);


Route::get('/videos', Videos::class)->name('videos');

Route::get('/tactics', Tactics::class)->name('tactics');

Route::get('/contact', Contact::class)->name('contact');

Route::get('/cards', CardsPage::class)->name('cards');

Route::get('/privacy-policy', PrivacyPolicy::class)->name('privacy-policy');
Route::get('/cookie-policy', CookiePolicy::class)->name('cookie-policy');

Route::get('/standings', FullStandingsPage::class)->name('standings');

Route::get('/player-ratings', PlayerRatingsPage::class)->name('player.ratings');
Route::get('/predictions/rankings', PredictionRankingsPage::class)->name('predictions.rankings');


Route::get('/debug-fb', function () {
    return dd(config('services.facebook'));
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', UserProfile::class)->name('profile');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
