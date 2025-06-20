<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Prediction;

class PredictionResult extends Model
{
    protected $guarded = ['id'];
    public function prediction()
    {
        return $this->belongsTo(Prediction::class);
    }
}
