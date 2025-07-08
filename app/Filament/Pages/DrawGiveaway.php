<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DrawGiveaway extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $title = 'Теглене на томбола';
    protected static ?string $navigationLabel = 'Теглене на томбола';
    protected static ?string $navigationGroup = 'Игри';

    protected static string $view = 'filament.pages.draw-giveaway';
}
