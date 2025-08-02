<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlyPlayerAward extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
