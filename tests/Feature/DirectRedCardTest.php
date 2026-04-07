<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectRedCardTest extends TestCase
{
    use RefreshDatabase;

    private function createPlayerWithCard(array $cardAttributes = []): Card
    {
        $team = Team::firstOrCreate(
            ['name' => 'Test Team'],
            ['country' => 'BG']
        );

        $player = Player::create([
            'name' => 'Test Player',
            'number' => 9,
            'position' => 'Forward',
            'team_id' => $team->id,
        ]);

        return Card::create(array_merge([
            'player_id' => $player->id,
            'yellow_cards' => 3,
            'second_yellow_reds' => 0,
            'direct_red_note' => null,
        ], $cardAttributes));
    }

    public function test_direct_red_note_column_exists(): void
    {
        $card = $this->createPlayerWithCard([
            'direct_red_note' => 'Директен червен за грубо влизане',
        ]);

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'direct_red_note' => 'Директен червен за грубо влизане',
        ]);
    }

    public function test_direct_red_note_is_nullable(): void
    {
        $card = $this->createPlayerWithCard(['direct_red_note' => null]);

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'direct_red_note' => null,
        ]);
    }

    public function test_has_direct_red_attribute_true_when_note_present(): void
    {
        $card = $this->createPlayerWithCard([
            'direct_red_note' => 'Фаул на последния защитник',
        ]);

        $this->assertTrue($card->has_direct_red);
    }

    public function test_has_direct_red_attribute_false_when_note_empty(): void
    {
        $card = $this->createPlayerWithCard(['direct_red_note' => null]);
        $this->assertFalse($card->has_direct_red);

        $card2 = $this->createPlayerWithCard(['direct_red_note' => '']);
        $this->assertFalse($card2->has_direct_red);
    }

    public function test_total_reds_does_not_count_note(): void
    {
        $card = $this->createPlayerWithCard([
            'direct_red_note' => 'Някаква бележка',
            'second_yellow_reds' => 2,
        ]);

        $this->assertEquals(2, $card->total_reds);
    }

    public function test_total_reds_without_direct_red_note(): void
    {
        $card = $this->createPlayerWithCard([
            'direct_red_note' => null,
            'second_yellow_reds' => 2,
        ]);

        $this->assertEquals(2, $card->total_reds);
    }

    public function test_cards_page_loads_successfully(): void
    {
        $this->createPlayerWithCard();

        $response = $this->get('/cards');
        $response->assertStatus(200);
    }

    public function test_cards_page_shows_star_for_direct_red(): void
    {
        $team = Team::firstOrCreate(['name' => 'CSKA'], ['country' => 'BG']);
        $player = Player::create([
            'name' => 'Иван Петров',
            'number' => 5,
            'position' => 'Defender',
            'team_id' => $team->id,
        ]);

        Card::create([
            'player_id' => $player->id,
            'yellow_cards' => 3,
            'second_yellow_reds' => 0,
            'direct_red_note' => 'Грубо влизане срещу Левски',
        ]);

        $response = $this->get('/cards');
        $response->assertStatus(200);
        $response->assertSee('Иван Петров');
        $response->assertSee('★');
        $response->assertSee('Грубо влизане срещу Левски');
    }

    public function test_cards_page_hides_legend_when_no_direct_reds(): void
    {
        $this->createPlayerWithCard(['direct_red_note' => null]);

        $response = $this->get('/cards');
        $response->assertStatus(200);
        $response->assertDontSee('Грубо влизане');
    }
}
