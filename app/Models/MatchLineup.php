<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchLineup extends Model
{
    protected $guarded = ['id'];

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class);
    }

    protected $with = ['player', 'replacesPlayer'];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class)->withTrashed();
    }

    public function replacesPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'replaces_player_id')->withTrashed();
    }
}
