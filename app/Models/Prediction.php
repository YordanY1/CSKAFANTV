<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\FootballMatch;
use App\Models\PredictionResult;

class Prediction extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function match()
    {
        return $this->belongsTo(FootballMatch::class, 'football_match_id');
    }

    public function result()
    {
        return $this->hasOne(PredictionResult::class);
    }
}
