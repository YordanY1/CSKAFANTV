<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\Home;
use App\Livewire\Components\UpcomingMatches;


Route::get('/', Home::class)->name('home');
Route::get('/matches/upcoming', UpcomingMatches::class)->name('matches.upcoming');
