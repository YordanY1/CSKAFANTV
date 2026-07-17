<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Player extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * URL for avatar-sized display: the square thumbnail when available,
     * falling back to the original photo. Null when the player has no photo.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $path = $this->image_thumb_path ?: $this->image_path;

        return $path ? Storage::disk('public')->url($path) : null;
    }
}
