<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded = ['id'];

    public function getYoutubeIdAttribute(): ?string
    {
        if (preg_match('/(?:\?v=|\/embed\/|\.be\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
