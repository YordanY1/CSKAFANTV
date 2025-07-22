<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Video extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function ($video) {
            if (empty($video->slug)) {
                $video->slug = Str::slug($video->title);
            }

            if (empty($video->category_slug) && $video->category) {
                $video->category_slug = Str::slug($video->category);
            }
        });

        static::updating(function ($video) {
            if ($video->isDirty('category')) {
                $video->category_slug = Str::slug($video->category);
            }
        });
    }


    public function getYoutubeIdAttribute(): ?string
    {
        if (preg_match('/(?:\?v=|\/embed\/|\.be\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
