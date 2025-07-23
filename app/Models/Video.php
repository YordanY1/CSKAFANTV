<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Video extends Model
{
    use HasSlug;

    protected $guarded = ['id'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }


    public function getYoutubeIdAttribute(): ?string
    {
        if (preg_match('/(?:\?v=|\/embed\/|\.be\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    protected static function booted(): void
    {
        static::creating(function ($video) {
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
}
