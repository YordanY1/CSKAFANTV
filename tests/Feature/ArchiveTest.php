<?php

namespace Tests\Feature;

use App\Livewire\Components\UpcomingMatches;
use App\Models\FootballMatch;
use App\Models\MonthlyPlayerAward;
use App\Models\Player;
use App\Models\PlayerReview;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\User;
use App\Support\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ArchiveTest extends TestCase
{
    use RefreshDatabase;

    private Team $cska;

    private FootballMatch $matchOld;

    private FootballMatch $matchVeryOld;

    private FootballMatch $matchNew;

    private Player $playerOld;

    private Player $playerNew;

    protected function setUp(): void
    {
        parent::setUp();

        // Fix "now" to the off-season so the active season is 2026-2027.
        Carbon::setTestNow(Carbon::parse('2026-06-17 12:00:00'));
        Http::fake(); // league standings widget must not hit the network
        config(['services.livescore.key' => 'test', 'services.livescore.secret' => 'test']);

        $this->cska = Team::create(['name' => 'ЦСКА София', 'country' => 'BG']);
        $opp = Team::create(['name' => 'Опонент', 'country' => 'BG']);

        // Archived season (2025-2026): a normal match + a very old one that must be folded in.
        $this->matchOld = FootballMatch::create([
            'home_team_id' => $this->cska->id, 'away_team_id' => $opp->id,
            'match_datetime' => '2025-09-15 18:00:00', 'is_finished' => true,
            'home_score' => 3, 'away_score' => 0, 'stadium' => 'СтадионСтар',
        ]);
        $this->matchVeryOld = FootballMatch::create([
            'home_team_id' => $this->cska->id, 'away_team_id' => $opp->id,
            'match_datetime' => '2024-03-01 18:00:00', 'is_finished' => true,
            'home_score' => 1, 'away_score' => 1, 'stadium' => 'СтадионМногоСтар',
        ]);

        // Active season (2026-2027): one finished + one upcoming.
        $this->matchNew = FootballMatch::create([
            'home_team_id' => $this->cska->id, 'away_team_id' => $opp->id,
            'match_datetime' => '2026-06-10 18:00:00', 'is_finished' => true,
            'home_score' => 1, 'away_score' => 0, 'stadium' => 'СтадионНов',
        ]);
        FootballMatch::create([
            'home_team_id' => $this->cska->id, 'away_team_id' => $opp->id,
            'match_datetime' => '2026-08-20 18:00:00', 'is_finished' => false,
            'stadium' => 'СтадионПредстоящ',
        ]);

        $this->playerOld = Player::create(['name' => 'ИгриачСтар', 'number' => 7, 'position' => 'Midfielder', 'team_id' => $this->cska->id, 'is_coach' => false]);
        $this->playerNew = Player::create(['name' => 'ИгриачНов', 'number' => 9, 'position' => 'Forward', 'team_id' => $this->cska->id, 'is_coach' => false]);

        $reviewer = User::factory()->create();
        PlayerReview::create(['user_id' => $reviewer->id, 'player_id' => $this->playerOld->id, 'match_id' => $this->matchOld->id, 'rating' => 7]);
        PlayerReview::create(['user_id' => $reviewer->id, 'player_id' => $this->playerNew->id, 'match_id' => $this->matchNew->id, 'rating' => 9]);

        MonthlyPlayerAward::create(['player_id' => $this->playerOld->id, 'month' => 9, 'year' => 2025, 'average_rating' => 8.00]);  // 2025-2026
        MonthlyPlayerAward::create(['player_id' => $this->playerNew->id, 'month' => 7, 'year' => 2026, 'average_rating' => 9.00]);  // 2026-2027

        $ivan = User::factory()->create(['name' => 'ПрогнозаИван']);
        $petar = User::factory()->create(['name' => 'ПрогнозаПетър']);
        Prediction::create(['user_id' => $ivan->id, 'football_match_id' => $this->matchOld->id, 'home_score_prediction' => 3, 'away_score_prediction' => 0]);
        Prediction::create(['user_id' => $petar->id, 'football_match_id' => $this->matchNew->id, 'home_score_prediction' => 1, 'away_score_prediction' => 0]);
        Artisan::call('predictions:calculate-points');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_season_helper_derives_and_floors_correctly(): void
    {
        $this->assertSame('2026-2027', Season::current());
        $this->assertSame('2025-2026', Season::fromDate('2025-09-15'));
        $this->assertSame('2025-2026', Season::fromDate('2024-03-01')); // floored
        $this->assertSame('2026-2027', Season::fromDate('2026-06-10'));
        $this->assertSame('2025-2026', Season::fromYearMonth(2025, 9));
        $this->assertSame('2026-2027', Season::fromYearMonth(2026, 7));
        $this->assertSame([0, 2026 * 12 + 6], Season::monthIndexBounds('2025-2026'));
        $this->assertSame([2026 * 12 + 6, 2027 * 12 + 6], Season::monthIndexBounds('2026-2027'));

        // Only the completed season is archived; the active season is excluded.
        $this->assertSame(['2025-2026'], Season::all());
    }

    public function test_match_seasons_are_auto_filled_with_floor(): void
    {
        $this->assertSame('2025-2026', $this->matchOld->season);
        $this->assertSame('2025-2026', $this->matchVeryOld->season); // folded into first season
        $this->assertSame('2026-2027', $this->matchNew->season);
    }

    public function test_form_offers_seasons_and_explicit_choice_is_respected(): void
    {
        $options = Season::formOptions();
        $this->assertArrayHasKey('2025-2026', $options);
        $this->assertArrayHasKey('2026-2027', $options); // active season is selectable
        $this->assertArrayHasKey('2027-2028', $options); // and the next one

        // An explicitly marked season is kept and not overwritten by the date.
        $match = FootballMatch::create([
            'home_team_id' => $this->cska->id, 'away_team_id' => $this->cska->id,
            'match_datetime' => '2026-09-01 18:00:00', 'is_finished' => false,
            'season' => '2025-2026',
        ]);
        $this->assertSame('2025-2026', $match->fresh()->season);
    }

    public function test_archive_holds_only_the_old_season_data(): void
    {
        $this->get('/archive/matches/2025-2026')
            ->assertStatus(200)
            ->assertSee('СтадионСтар')
            ->assertSee('СтадионМногоСтар')
            ->assertDontSee('СтадионНов');

        $this->get('/archive/player-ratings/2025-2026')
            ->assertStatus(200)->assertSee('ИгриачСтар')->assertDontSee('ИгриачНов');

        $this->get('/archive/hall-of-fame/2025-2026')
            ->assertStatus(200)->assertSee('ИгриачСтар')->assertDontSee('ИгриачНов');

        $this->get('/archive/prediction-rankings/2025-2026')
            ->assertStatus(200)->assertSee('ПрогнозаИван')->assertDontSee('ПрогнозаПетър');
    }

    public function test_live_pages_show_only_the_active_season(): void
    {
        $this->get('/player-ratings')
            ->assertStatus(200)->assertSee('ИгриачНов')->assertDontSee('ИгриачСтар');

        $this->get('/hall-of-fame')
            ->assertStatus(200)->assertSee('ИгриачНов')->assertDontSee('ИгриачСтар');

        $this->get('/predictions/rankings')
            ->assertStatus(200)->assertSee('ПрогнозаПетър')->assertDontSee('ПрогнозаИван');

        Livewire::test(UpcomingMatches::class)
            ->set('filter', 'completed')
            ->assertSee('СтадионНов')
            ->assertDontSee('СтадионСтар')
            ->assertDontSee('СтадионМногоСтар');
    }

    public function test_home_page_is_scoped_to_the_active_season(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('ИгриачНов')
            ->assertDontSee('ИгриачСтар');
    }

    public function test_archive_index_lists_only_completed_seasons(): void
    {
        $this->get('/archive')
            ->assertStatus(200)
            ->assertSee('Сезон 2025-2026')
            ->assertDontSee('Сезон 2026-2027');
    }

    public function test_invalid_season_404_and_empty_season_renders(): void
    {
        $this->get('/archive/matches/not-a-season')->assertStatus(404);
        $this->get('/archive/matches/2030-2031')
            ->assertStatus(200)
            ->assertSee('Няма изиграни мачове');
    }
}
