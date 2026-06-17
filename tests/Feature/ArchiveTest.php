<?php

namespace Tests\Feature;

use App\Models\FootballMatch;
use App\Models\MonthlyPlayerAward;
use App\Models\Player;
use App\Models\PlayerReview;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\User;
use App\Support\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ArchiveTest extends TestCase
{
    use RefreshDatabase;

    private Team $cska;

    private Team $opp2024;

    private Team $opp2025;

    private FootballMatch $match2024;

    private FootballMatch $match2025;

    private FootballMatch $match2025Open;

    private Player $player2024;

    private Player $player2025;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cska = Team::create(['name' => 'ЦСКА София', 'country' => 'BG']);
        $this->opp2024 = Team::create(['name' => 'Опонент24', 'country' => 'BG']);
        $this->opp2025 = Team::create(['name' => 'Опонент25', 'country' => 'BG']);

        // Two finished matches in different seasons + one unfinished current-season match.
        $this->match2024 = FootballMatch::create([
            'home_team_id' => $this->cska->id,
            'away_team_id' => $this->opp2024->id,
            'match_datetime' => '2024-09-15 18:00:00',
            'is_finished' => true,
            'home_score' => 2,
            'away_score' => 1,
            'stadium' => 'СтадионС2024',
        ]);

        $this->match2025 = FootballMatch::create([
            'home_team_id' => $this->cska->id,
            'away_team_id' => $this->opp2025->id,
            'match_datetime' => '2025-09-15 18:00:00',
            'is_finished' => true,
            'home_score' => 3,
            'away_score' => 0,
            'stadium' => 'СтадионС2025',
        ]);

        $this->match2025Open = FootballMatch::create([
            'home_team_id' => $this->cska->id,
            'away_team_id' => $this->opp2025->id,
            'match_datetime' => '2026-04-20 18:00:00',
            'is_finished' => false,
            'stadium' => 'СтадионОтворен',
        ]);

        $this->player2024 = Player::create([
            'name' => 'Играч_С2024', 'number' => 10, 'position' => 'Midfielder', 'team_id' => $this->cska->id,
        ]);
        $this->player2025 = Player::create([
            'name' => 'Играч_С2025', 'number' => 9, 'position' => 'Forward', 'team_id' => $this->cska->id,
        ]);

        $reviewer = User::factory()->create();

        PlayerReview::create([
            'user_id' => $reviewer->id, 'player_id' => $this->player2024->id,
            'match_id' => $this->match2024->id, 'rating' => 5,
        ]);
        PlayerReview::create([
            'user_id' => $reviewer->id, 'player_id' => $this->player2025->id,
            'match_id' => $this->match2025->id, 'rating' => 8,
        ]);

        // Hall of fame: month 3/2025 belongs to season 2024-2025, month 9/2025 to 2025-2026.
        MonthlyPlayerAward::create(['player_id' => $this->player2024->id, 'month' => 3, 'year' => 2025, 'average_rating' => 7.00]);
        MonthlyPlayerAward::create(['player_id' => $this->player2025->id, 'month' => 9, 'year' => 2025, 'average_rating' => 8.50]);

        // Prediction rankings per season.
        $ivan = User::factory()->create(['name' => 'ПрогнозаИван']);
        $petar = User::factory()->create(['name' => 'ПрогнозаПетър']);

        Prediction::create([
            'user_id' => $ivan->id, 'football_match_id' => $this->match2025->id,
            'home_score_prediction' => 3, 'away_score_prediction' => 0,
        ]);
        Prediction::create([
            'user_id' => $petar->id, 'football_match_id' => $this->match2024->id,
            'home_score_prediction' => 2, 'away_score_prediction' => 1,
        ]);

        Artisan::call('predictions:calculate-points');
    }

    public function test_season_helper_derives_labels_correctly(): void
    {
        $this->assertSame('2025-2026', Season::fromDate('2025-09-15'));
        $this->assertSame('2024-2025', Season::fromDate('2025-06-30'));
        $this->assertSame('2025-2026', Season::fromDate('2026-05-01'));
        $this->assertSame('2024-2025', Season::fromYearMonth(2025, 3));
        $this->assertSame('2025-2026', Season::fromYearMonth(2025, 9));
        $this->assertSame([2025 * 12 + 7, 2026 * 12 + 7], Season::monthIndexBounds('2025-2026'));
        $this->assertTrue(Season::isValid('2025-2026'));
        $this->assertFalse(Season::isValid('not-a-season'));
        $this->assertSame(['2025-2026', '2024-2025'], Season::all());
    }

    public function test_match_season_is_auto_filled_and_overridable(): void
    {
        $this->assertSame('2024-2025', $this->match2024->season);
        $this->assertSame('2025-2026', $this->match2025->season);
        $this->assertSame('2025-2026', $this->match2025Open->season);

        $auto = FootballMatch::create([
            'home_team_id' => $this->cska->id, 'away_team_id' => $this->opp2024->id,
            'match_datetime' => '2027-08-01 18:00:00', 'is_finished' => false,
        ]);
        $this->assertSame('2027-2028', $auto->season);

        $override = FootballMatch::create([
            'home_team_id' => $this->cska->id, 'away_team_id' => $this->opp2024->id,
            'match_datetime' => '2027-08-01 18:00:00', 'is_finished' => false,
            'season' => '2099-2100',
        ]);
        $this->assertSame('2099-2100', $override->fresh()->season);
    }

    public function test_archive_index_lists_seasons(): void
    {
        $this->get('/archive')
            ->assertStatus(200)
            ->assertSee('Сезон 2025-2026')
            ->assertSee('Сезон 2024-2025');
    }

    public function test_archive_matches_are_scoped_to_season_and_finished_only(): void
    {
        $this->get('/archive/matches/2025-2026')
            ->assertStatus(200)
            ->assertSee('СтадионС2025')
            ->assertDontSee('СтадионС2024')
            ->assertDontSee('СтадионОтворен'); // unfinished excluded

        $this->get('/archive/matches/2024-2025')
            ->assertStatus(200)
            ->assertSee('СтадионС2024')
            ->assertDontSee('СтадионС2025');
    }

    public function test_archive_player_ratings_are_scoped_to_season(): void
    {
        $this->get('/archive/player-ratings/2025-2026')
            ->assertStatus(200)
            ->assertSee('Играч_С2025')
            ->assertDontSee('Играч_С2024');

        $this->get('/archive/player-ratings/2024-2025')
            ->assertStatus(200)
            ->assertSee('Играч_С2024')
            ->assertDontSee('Играч_С2025');
    }

    public function test_archive_hall_of_fame_is_scoped_to_season(): void
    {
        $this->get('/archive/hall-of-fame/2025-2026')
            ->assertStatus(200)
            ->assertSee('Играч_С2025')
            ->assertDontSee('Играч_С2024');

        $this->get('/archive/hall-of-fame/2024-2025')
            ->assertStatus(200)
            ->assertSee('Играч_С2024')
            ->assertDontSee('Играч_С2025');
    }

    public function test_archive_prediction_rankings_are_scoped_to_season(): void
    {
        $this->get('/archive/prediction-rankings/2025-2026')
            ->assertStatus(200)
            ->assertSee('ПрогнозаИван')
            ->assertDontSee('ПрогнозаПетър');

        $this->get('/archive/prediction-rankings/2024-2025')
            ->assertStatus(200)
            ->assertSee('ПрогнозаПетър')
            ->assertDontSee('ПрогнозаИван');
    }

    public function test_invalid_season_returns_404_and_empty_season_renders(): void
    {
        $this->get('/archive/matches/not-a-season')->assertStatus(404);
        $this->get('/archive/matches/2030-2031')
            ->assertStatus(200)
            ->assertSee('Няма изиграни мачове');
    }

    public function test_live_pages_remain_unscoped_and_show_all_seasons(): void
    {
        // The existing public pages must be untouched: they still show every season.
        $this->get('/player-ratings')
            ->assertStatus(200)
            ->assertSee('Играч_С2025')
            ->assertSee('Играч_С2024');
    }
}
