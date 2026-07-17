<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Team;
use App\Services\PlayerThumbnailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlayerThumbnailTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->team = Team::create(['name' => 'ЦСКА', 'country' => 'BG']);
    }

    /** Writes a real PNG (portrait, 100x150) to the fake public disk. */
    private function putFakePhoto(string $path): void
    {
        $img = imagecreatetruecolor(100, 150);
        $red = imagecolorallocate($img, 200, 30, 30);
        imagefilledrectangle($img, 0, 0, 100, 150, $red);

        ob_start();
        imagepng($img);
        $binary = ob_get_clean();

        Storage::disk('public')->put($path, $binary);
    }

    public function test_thumbnail_is_generated_when_player_is_created_with_photo(): void
    {
        $this->putFakePhoto('players/test.png');

        $player = Player::create([
            'name' => 'Тест Играч',
            'team_id' => $this->team->id,
            'image_path' => 'players/test.png',
        ]);

        $this->assertSame('players/thumbs/test.webp', $player->image_thumb_path);
        Storage::disk('public')->assertExists('players/thumbs/test.webp');

        [$width, $height] = getimagesize(Storage::disk('public')->path('players/thumbs/test.webp'));
        $this->assertSame(PlayerThumbnailService::SIZE, $width);
        $this->assertSame(PlayerThumbnailService::SIZE, $height);
    }

    public function test_thumbnail_is_regenerated_and_old_one_deleted_when_photo_changes(): void
    {
        $this->putFakePhoto('players/old.png');
        $this->putFakePhoto('players/new.png');

        $player = Player::create([
            'name' => 'Тест Играч',
            'team_id' => $this->team->id,
            'image_path' => 'players/old.png',
        ]);

        $player->update(['image_path' => 'players/new.png']);

        $this->assertSame('players/thumbs/new.webp', $player->image_thumb_path);
        Storage::disk('public')->assertExists('players/thumbs/new.webp');
        Storage::disk('public')->assertMissing('players/thumbs/old.webp');
    }

    public function test_thumbnail_is_removed_when_photo_is_removed(): void
    {
        $this->putFakePhoto('players/test.png');

        $player = Player::create([
            'name' => 'Тест Играч',
            'team_id' => $this->team->id,
            'image_path' => 'players/test.png',
        ]);

        $player->update(['image_path' => null]);

        $this->assertNull($player->image_thumb_path);
        Storage::disk('public')->assertMissing('players/thumbs/test.webp');
    }

    public function test_missing_source_file_does_not_break_saving(): void
    {
        $player = Player::create([
            'name' => 'Тест Играч',
            'team_id' => $this->team->id,
            'image_path' => 'players/does-not-exist.png',
        ]);

        $this->assertNull($player->image_thumb_path);
    }

    public function test_command_backfills_thumbnails_for_existing_players(): void
    {
        $this->putFakePhoto('players/backfill.png');

        Player::withoutEvents(function () {
            Player::create([
                'name' => 'Стар Играч',
                'team_id' => $this->team->id,
                'image_path' => 'players/backfill.png',
            ]);
        });

        $this->assertNull(Player::first()->image_thumb_path);

        $this->artisan('players:generate-thumbnails')
            ->expectsOutputToContain('Generated: 1')
            ->assertSuccessful();

        $this->assertSame('players/thumbs/backfill.webp', Player::first()->image_thumb_path);
        Storage::disk('public')->assertExists('players/thumbs/backfill.webp');
    }

    public function test_avatar_url_prefers_thumbnail_and_falls_back_to_original(): void
    {
        $this->putFakePhoto('players/test.png');

        $player = Player::create([
            'name' => 'Тест Играч',
            'team_id' => $this->team->id,
            'image_path' => 'players/test.png',
        ]);

        $this->assertStringContainsString('players/thumbs/test.webp', $player->avatar_url);

        $player->image_thumb_path = null;
        $this->assertStringContainsString('players/test.png', $player->avatar_url);

        $player->image_path = null;
        $this->assertNull($player->avatar_url);
    }
}
