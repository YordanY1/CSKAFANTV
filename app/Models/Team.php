<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Player;
use App\Models\FootballMatch;
use App\Models\Standing;

class Team extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['full_title'];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(FootballMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(FootballMatch::class, 'away_team_id');
    }

    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class);
    }

    public function getFullTitleAttribute(): string
    {
        return $this->name . ($this->stadium ? ' | ' . $this->stadium : '');
    }
}
