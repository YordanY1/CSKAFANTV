<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Alignment;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Throwable;

class PlayerThumbnailService
{
    /**
     * Square edge in px. Covers the largest avatar on the site (w-28 = 112px)
     * at 3x device pixel ratio, so browsers never downscale more than ~3.5x.
     */
    public const SIZE = 384;

    public const DIRECTORY = 'players/thumbs';

    public const QUALITY = 82;

    /**
     * Generate a square WebP thumbnail for a photo on the public disk.
     * Returns the thumbnail path relative to the disk, or null on failure.
     */
    public function generate(string $sourcePath): ?string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($sourcePath)) {
            return null;
        }

        try {
            $encoded = ImageManager::usingDriver(GdDriver::class)
                ->decodePath($disk->path($sourcePath))
                ->cover(self::SIZE, self::SIZE, Alignment::CENTER)
                ->encodeUsingFormat(Format::WEBP, quality: self::QUALITY);
        } catch (Throwable $e) {
            Log::warning("Player thumbnail generation failed for {$sourcePath}: {$e->getMessage()}");

            return null;
        }

        $thumbPath = self::DIRECTORY.'/'.pathinfo($sourcePath, PATHINFO_FILENAME).'.webp';

        // The public disk is configured with throw => false, so a failed
        // write returns false instead of throwing.
        if (! $disk->put($thumbPath, $encoded->toString())) {
            Log::warning("Player thumbnail write failed for {$thumbPath}");

            return null;
        }

        return $thumbPath;
    }

    public function delete(?string $thumbPath): void
    {
        if ($thumbPath) {
            Storage::disk('public')->delete($thumbPath);
        }
    }
}
