<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TacticBoardPlayersApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The endpoint orders with MySQL's FIELD(); emulate it on SQLite.
        DB::connection()->getPdo()->sqliteCreateFunction('FIELD', function (...$args) {
            $value = array_shift($args);
            $position = array_search($value, $args, true);

            return $position === false ? 0 : $position + 1;
        });
    }

    public function test_players_api_excludes_coaching_staff(): void
    {
        $team = Team::create(['name' => 'ЦСКА', 'country' => 'BG']);

        Player::create(['name' => 'Иван Играч', 'team_id' => $team->id, 'position' => 'Вратар', 'is_coach' => false]);
        Player::create(['name' => 'Христо Наставник', 'team_id' => $team->id, 'position' => 'Старши треньор', 'is_coach' => false]);
        Player::create(['name' => 'Петър Стоянов', 'team_id' => $team->id, 'position' => 'Анализатор', 'is_coach' => false]);
        Player::create(['name' => 'Георги Щабен', 'team_id' => $team->id, 'position' => 'Вратар', 'is_coach' => true]);

        $names = $this->getJson('/api/players')->assertOk()->json('*.name');

        $this->assertSame(['Иван Играч'], $names);
    }
}
