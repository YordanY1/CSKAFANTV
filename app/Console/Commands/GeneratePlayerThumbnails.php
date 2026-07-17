<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Services\PlayerThumbnailService;
use Illuminate\Console\Command;

class GeneratePlayerThumbnails extends Command
{
    protected $signature = 'players:generate-thumbnails {--force : Regenerate even if a thumbnail already exists}';

    protected $description = 'Generate square avatar thumbnails for player photos';

    public function handle(PlayerThumbnailService $thumbnails): int
    {
        $generated = 0;
        $skipped = 0;
        $failed = 0;

        $players = Player::withTrashed()->whereNotNull('image_path')->cursor();

        foreach ($players as $player) {
            if ($player->image_thumb_path && ! $this->option('force')) {
                $skipped++;

                continue;
            }

            $thumbPath = $thumbnails->generate($player->image_path);

            if ($thumbPath === null) {
                $this->warn("Failed: {$player->name} ({$player->image_path})");
                $failed++;

                continue;
            }

            $player->image_thumb_path = $thumbPath;
            $player->saveQuietly();
            $generated++;
        }

        $this->info("Generated: {$generated}, skipped: {$skipped}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
