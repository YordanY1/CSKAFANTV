<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Team;

class Player extends Model
{
    protected $guarded = ['id'];
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
