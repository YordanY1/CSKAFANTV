<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Team;
use App\Models\PlayerReview;
use Illuminate\Support\Facades\Log;



class FootballMatch extends Model
{
    protected $table = 'football_matches';
    protected $guarded = ['id'];
    protected $casts = [
        'match_datetime' => 'datetime',
        'is_finished' => 'boolean',
    ];

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function reviews()
    {
        return $this->hasMany(PlayerReview::class);
    }

    public function lineup()
    {
        return $this->hasMany(MatchLineup::class);
    }

}
