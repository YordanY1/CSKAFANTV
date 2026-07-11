<?php

namespace Tests\Feature;

use App\Services\LiveScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LiveScoreServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-07-11 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function seasonsResponse(): array
    {
        return [
            'success' => true,
            'data' => [
                'seasons' => [
                    ['id' => 57, 'name' => '2026/2027', 'start' => '2026-07-01', 'end' => '2027-06-30'],
                    ['id' => 52, 'name' => '2026', 'start' => '2026-01-01', 'end' => '2026-12-31'],
                    ['id' => 56, 'name' => '2025/2026', 'start' => '2025-07-01', 'end' => '2026-06-30'],
                ],
            ],
        ];
    }

    private function tableResponse(array $standings): array
    {
        return [
            'success' => true,
            'data' => [
                'season' => ['id' => 57, 'name' => '2026/2027'],
                'stages' => [
                    ['stage' => ['name' => 'Regular Season'], 'groups' => [['name' => 'A', 'standings' => $standings]]],
                ],
            ],
        ];
    }

    private function standingRow(int $teamId, string $name, int $points, int $matches): array
    {
        return [
            'rank' => 1,
            'points' => $points,
            'matches' => $matches,
            'won' => $points > 0 ? intdiv($points, 3) : 0,
            'drawn' => 0,
            'lost' => 0,
            'goal_diff' => $points,
            'goals_scored' => $points,
            'goals_conceded' => 0,
            'team' => ['id' => $teamId, 'name' => $name, 'stadium' => null, 'logo' => "https://cdn.example/{$teamId}.png"],
        ];
    }

    public function test_requests_table_pinned_to_the_season_covering_today(): void
    {
        Http::fake([
            '*/seasons/list.json*' => Http::response($this->seasonsResponse()),
            '*/competitions/table.json*' => Http::response($this->tableResponse([
                $this->standingRow(918, 'Levski Sofia', 3, 1),
            ])),
        ]);

        $standings = app(LiveScoreService::class)->getStandingsWithTeams(71);

        Http::assertSent(
            fn ($request) => str_contains($request->url(), 'competitions/table.json')
                && $request->data()['season_id'] === 57
        );

        $this->assertCount(1, $standings);
        $this->assertSame('Levski Sofia', $standings[0]['name']);
    }

    public function test_empty_new_season_falls_back_to_zeroed_table_from_fixtures(): void
    {
        Http::fake([
            '*/seasons/list.json*' => Http::response($this->seasonsResponse()),
            '*/competitions/table.json*' => Http::response($this->tableResponse([])),
            '*/fixtures/matches.json*' => Http::response([
                'success' => true,
                'data' => [
                    'fixtures' => [
                        ['home_id' => 918, 'home_name' => 'Levski Sofia', 'away_id' => 1395, 'away_name' => 'PFC CSKA-Sofia'],
                        ['home_id' => 155, 'home_name' => 'Ludogorets Razgrad', 'away_id' => 918, 'away_name' => 'Levski Sofia'],
                    ],
                ],
            ]),
        ]);

        $standings = app(LiveScoreService::class)->getStandingsWithTeams(71);

        $this->assertCount(3, $standings);

        foreach ($standings as $row) {
            $this->assertSame(0, $row['points']);
            $this->assertSame(0, $row['matches']);
        }

        // Zeroed rows keep a stable alphabetical order and get sequential ranks.
        $this->assertSame([1, 2, 3], array_column($standings, 'rank'));
        $this->assertSame(
            ['Levski Sofia', 'Ludogorets Razgrad', 'PFC CSKA-Sofia'],
            array_column($standings, 'name')
        );
    }

    public function test_empty_season_without_fixtures_falls_back_to_default_season_table(): void
    {
        Http::fake([
            '*/seasons/list.json*' => Http::response($this->seasonsResponse()),
            '*/fixtures/matches.json*' => Http::response(['success' => true, 'data' => ['fixtures' => []]]),
            '*/competitions/table.json*' => Http::sequence()
                ->push($this->tableResponse([]))
                ->push($this->tableResponse([
                    $this->standingRow(918, 'Levski Sofia', 81, 36),
                ])),
        ]);

        $standings = app(LiveScoreService::class)->getStandingsWithTeams(71);

        // Second table request must be the un-pinned (default season) one.
        Http::assertSentCount(4);
        $this->assertCount(1, $standings);
        $this->assertSame(81, $standings[0]['points']);
    }

    public function test_dedupes_teams_across_stages_keeping_most_played_row(): void
    {
        $response = $this->tableResponse([]);
        $response['data']['stages'] = [
            [
                'stage' => ['name' => 'Regular Season'],
                'groups' => [['name' => 'A', 'standings' => [
                    $this->standingRow(918, 'Levski Sofia', 70, 30),
                    $this->standingRow(1395, 'PFC CSKA-Sofia', 56, 30),
                ]]],
            ],
            [
                'stage' => ['name' => 'Championship Round'],
                'groups' => [['name' => '1', 'standings' => [
                    $this->standingRow(918, 'Levski Sofia', 81, 36),
                ]]],
            ],
        ];

        Http::fake([
            '*/seasons/list.json*' => Http::response($this->seasonsResponse()),
            '*/competitions/table.json*' => Http::response($response),
        ]);

        $standings = app(LiveScoreService::class)->getStandingsWithTeams(71);

        $this->assertCount(2, $standings);
        $this->assertSame(81, $standings[0]['points']);
        $this->assertSame(1, $standings[0]['rank']);
        $this->assertSame(2, $standings[1]['rank']);
    }

    public function test_standings_are_cached_per_competition(): void
    {
        Http::fake([
            '*/seasons/list.json*' => Http::response($this->seasonsResponse()),
            '*/competitions/table.json*' => Http::response($this->tableResponse([
                $this->standingRow(918, 'Levski Sofia', 3, 1),
            ])),
        ]);

        $service = app(LiveScoreService::class);
        $service->getStandingsWithTeams(71);
        $service->getStandingsWithTeams(71);

        Http::assertSentCount(2); // seasons list + one table request
    }

    public function test_table_api_failure_returns_empty_and_never_builds_zeroed_table(): void
    {
        Http::fake([
            '*/seasons/list.json*' => Http::response($this->seasonsResponse()),
            '*/competitions/table.json*' => Http::response(['success' => false]),
            '*/fixtures/matches.json*' => Http::response([
                'success' => true,
                'data' => ['fixtures' => [
                    ['home_id' => 918, 'home_name' => 'Levski Sofia', 'away_id' => 1395, 'away_name' => 'PFC CSKA-Sofia'],
                ]],
            ]),
        ]);

        $service = app(LiveScoreService::class);

        // A failed request must not be mistaken for an empty season — no
        // zeroed table built from fixtures.
        $this->assertSame([], $service->getStandingsWithTeams(71));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'fixtures/matches.json'));

        // The failure is negative-cached briefly (no upstream hammering)…
        $service->getStandingsWithTeams(71);
        Http::assertSentCount(2); // seasons list + one table attempt

        // …and retried once the short TTL expires.
        Carbon::setTestNow(Carbon::getTestNow()->addMinutes(2));
        $service->getStandingsWithTeams(71);
        Http::assertSentCount(3);
    }

    public function test_failed_seasons_list_is_not_cached_for_a_day(): void
    {
        Http::fake([
            '*/seasons/list.json*' => Http::response(['success' => false]),
            '*/competitions/table.json*' => Http::response($this->tableResponse([
                $this->standingRow(918, 'Levski Sofia', 81, 36),
            ])),
        ]);

        $standings = app(LiveScoreService::class)->getStandingsWithTeams(71);

        // Season pinning is skipped for this request (un-pinned table)…
        Http::assertSent(
            fn ($request) => str_contains($request->url(), 'competitions/table.json')
                && ! array_key_exists('season_id', $request->data())
        );
        $this->assertSame(81, $standings[0]['points']);

        // …but the failure must not disable pinning for a whole day.
        $this->assertFalse(Cache::has('livescore.seasons'));
    }

    public function test_connection_failure_degrades_gracefully_instead_of_throwing(): void
    {
        Http::fake([
            '*/seasons/list.json*' => Http::failedConnection(),
            '*/competitions/table.json*' => Http::response($this->tableResponse([
                $this->standingRow(918, 'Levski Sofia', 81, 36),
            ])),
        ]);

        $standings = app(LiveScoreService::class)->getStandingsWithTeams(71);

        $this->assertSame(81, $standings[0]['points']);
        $this->assertFalse(Cache::has('livescore.seasons'));
    }

    public function test_full_outage_returns_empty_standings_without_exception(): void
    {
        Http::fake(['*' => Http::failedConnection()]);

        $this->assertSame([], app(LiveScoreService::class)->getStandingsWithTeams(71));
    }
}
