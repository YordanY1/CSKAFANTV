<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyPlayerAward extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class)->withTrashed();
    }
}
