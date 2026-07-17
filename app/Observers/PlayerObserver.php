<?php

namespace App\Observers;

use App\Models\Player;
use App\Services\PlayerThumbnailService;

class PlayerObserver
{
    public function __construct(private PlayerThumbnailService $thumbnails) {}

    public function updating(Player $player): void
    {
        if ($player->isDirty('image_path')) {
            $this->thumbnails->delete($player->getOriginal('image_thumb_path'));
            $player->image_thumb_path = null;
        }
    }

    public function saved(Player $player): void
    {
        if ($player->image_path && ! $player->image_thumb_path) {
            $player->image_thumb_path = $this->thumbnails->generate($player->image_path);

            if ($player->image_thumb_path) {
                $player->saveQuietly();
            }
        }
    }

    public function forceDeleted(Player $player): void
    {
        $this->thumbnails->delete($player->image_thumb_path);
    }
}
