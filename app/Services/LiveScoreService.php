<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LiveScoreService
{
    protected string $baseUrl = 'https://livescore-api.com/api-client';

    protected string $key;

    protected string $secret;

    public function __construct()
    {
        $this->key = config('services.livescore.key');
        $this->secret = config('services.livescore.secret');
    }

    public function getStandingsWithTeams(int $competitionId = 71): array
    {
        return $this->fetchStandings($competitionId);
    }

    public function getSecondLeagueStandings(): array
    {
        return $this->fetchStandings(140);
    }

    protected function fetchStandings(int $competitionId): array
    {
        $cacheKey = "livescore.standings.{$competitionId}";

        $standings = Cache::get($cacheKey);

        if ($standings === null) {
            $standings = $this->buildStandings($competitionId);

            // An empty result means the API failed. Cache it only briefly:
            // the site retries within a minute instead of either serving the
            // failure for 10 minutes or hammering a rate-limited upstream on
            // every page view.
            Cache::put(
                $cacheKey,
                $standings,
                $standings === [] ? now()->addMinute() : now()->addMinutes(10)
            );
        }

        return $standings;
    }

    protected function buildStandings(int $competitionId): array
    {
        $seasonId = $this->currentSeasonId();

        $entries = $this->fetchTableEntries($competitionId, $seasonId);

        // A failed request is NOT an empty season — bail out rather than
        // guess (a zeroed table mid-season would be worse than none).
        if ($entries === null) {
            return [];
        }

        if ($entries->isEmpty() && $seasonId !== null) {
            // The current season exists in the API but has no played rounds
            // yet — show its participants (from announced fixtures) with
            // zeroed stats instead of last season's final table.
            $entries = $this->zeroedEntriesFromFixtures($competitionId);

            if ($entries->isEmpty()) {
                // No fixtures announced either — fall back to whatever season
                // the API still serves by default so the page is never blank.
                $entries = $this->fetchTableEntries($competitionId, null) ?? collect();
            }
        }

        if ($entries->isEmpty()) {
            return [];
        }

        $localTeams = DB::table('teams')
            ->select('id', 'name', 'logo', 'stadium', 'manager', 'external_id')
            ->get()
            ->keyBy('external_id');

        // First League returns multiple stages (regular season + championship /
        // 5-8 / relegation playoff groups). Each team appears once per stage,
        // so dedupe by team id and keep the entry with the most matches played
        // — that's the live playoff row, not the stale regular-season one.
        $deduped = $entries
            ->groupBy(fn ($item) => $item['team']['id'] ?? null)
            ->map(fn ($rows) => $rows->sortByDesc(fn ($r) => (int) ($r['matches'] ?? 0))->first())
            ->values();

        // Playoff groups restart rank at 1 each, so recompute a global rank by
        // points → goal difference → goals scored.
        $sorted = $deduped->sort(function ($a, $b) {
            if ((int) $a['points'] !== (int) $b['points']) {
                return (int) $b['points'] <=> (int) $a['points'];
            }
            if ((int) $a['goal_diff'] !== (int) $b['goal_diff']) {
                return (int) $b['goal_diff'] <=> (int) $a['goal_diff'];
            }

            return (int) $b['goals_scored'] <=> (int) $a['goals_scored'];
        })->values();

        return $sorted->map(function ($item, $index) use ($localTeams) {
            $item['rank'] = $index + 1;

            return $this->mapTeamData($item, $localTeams);
        })->toArray();
    }

    /**
     * Returns null when the request fails (transport error or success:false),
     * an empty collection when the season genuinely has no standings yet.
     */
    protected function fetchTableEntries(int $competitionId, ?int $seasonId): ?Collection
    {
        $params = [
            'competition_id' => $competitionId,
            'lang' => 'bg',
            'include_form' => 1,
        ];

        if ($seasonId !== null) {
            $params['season_id'] = $seasonId;
        }

        $response = $this->apiGet('competitions/table.json', $params);

        if ($response === null) {
            return null;
        }

        return collect(data_get($response, 'data.stages', []))
            ->flatMap(
                fn ($stage) => collect($stage['groups'] ?? [])->flatMap(fn ($group) => $group['standings'] ?? [])
            );
    }

    protected function apiGet(string $endpoint, array $params): ?array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/{$endpoint}", $params + [
                    'key' => $this->key,
                    'secret' => $this->secret,
                ])
                ->json();
        } catch (ConnectionException) {
            return null;
        }

        return data_get($response, 'success') ? $response : null;
    }

    /**
     * Without an explicit season_id the table endpoint returns the last season
     * that has standings data — i.e. last season's final table until the first
     * round of the new season is played. Resolve the season that covers today
     * so we can pin the request to it. Bulgarian leagues use cross-year
     * seasons ("2026/2027"); the calendar-year entries belong to other
     * competitions.
     */
    protected function currentSeasonId(): ?int
    {
        $seasons = Cache::remember(
            'livescore.seasons',
            now()->addDay(),
            fn () => data_get($this->apiGet('seasons/list.json', []), 'data.seasons', [])
        );

        // A failed fetch must not disable season pinning for a whole day —
        // drop it so the next rebuild retries (the standings cache above
        // already rate-limits how often that can happen).
        if ($seasons === []) {
            Cache::forget('livescore.seasons');
        }

        $today = now()->toDateString();

        $season = collect($seasons)->first(
            fn ($season) => str_contains($season['name'] ?? '', '/')
                && ($season['start'] ?? '') <= $today
                && $today <= ($season['end'] ?? '')
        );

        return isset($season['id']) ? (int) $season['id'] : null;
    }

    protected function zeroedEntriesFromFixtures(int $competitionId): Collection
    {
        $response = $this->apiGet('fixtures/matches.json', [
            'competition_id' => $competitionId,
        ]);

        if ($response === null) {
            return collect();
        }

        return collect(data_get($response, 'data.fixtures', []))
            ->flatMap(fn ($fixture) => [
                ['id' => $fixture['home_id'] ?? null, 'name' => $fixture['home_name'] ?? null],
                ['id' => $fixture['away_id'] ?? null, 'name' => $fixture['away_name'] ?? null],
            ])
            ->filter(fn ($team) => $team['id'] && $team['name'])
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(fn ($team) => [
                'rank' => 0,
                'points' => 0,
                'matches' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goal_diff' => 0,
                'goals_scored' => 0,
                'goals_conceded' => 0,
                'form' => [],
                'team' => [
                    'id' => (int) $team['id'],
                    'name' => $team['name'],
                    'logo' => "https://cdn.live-score-api.com/teams/{$team['id']}.png",
                ],
            ]);
    }

    protected function mapTeamData(array $item, $localTeams): array
    {
        $translations = [
            'Dunav Ruse' => 'Дунав Русе',
            'Fratria' => 'Фратрия',
            'Yantra' => 'Янтра',
            'Vihren Sandanski' => 'Вихрен Сандански',
            'Pirin Blagoevgrad' => 'Пирин Благоевград',
            'Lokomotiv Gorna Oryahovitsa' => 'Локомотив Горна Оряховица',
            'CSKA Sofia II' => 'ЦСКА II',
            'Minyor Pernik' => 'Миньор Перник',
            'Hebar Pazardzhik' => 'Хебър Пазарджик',
            'Chernomorets Burgas' => 'Черноморец Бургас',
            'Sevlievo' => 'Севлиево',
            'Ludogorets II' => 'Лудогорец II',
            'Spartak Pleven' => 'Спартак Плевен',
            'Etar' => 'Етър',
            'Marek' => 'Марек',
            'Sportist Svoge' => 'Спортист Своге',
            'Belasitsa Petrich' => 'Беласица Петрич',
        ];

        $externalId = $item['team']['id'] ?? null;
        $local = $externalId ? $localTeams->get($externalId) : null;
        $originalName = $item['team']['name'] ?? null;

        $translated = $local->name ?? ($translations[$originalName] ?? $originalName);

        if (in_array($originalName, ['CSKA Sofia II', 'CSKA 2', 'CSKA II'])) {
            $translated = 'ЦСКА II';

            $cskaRecord = DB::table('teams')
                ->where('name', 'like', '%ЦСКА%')
                ->first();

            if ($cskaRecord && ! empty($cskaRecord->logo)) {
                $logo = asset('storage/'.ltrim($cskaRecord->logo, '/'));
            } else {
                $logo = asset('images/cska.png');
            }
        } elseif (in_array($originalName, ['Vihren Sandanski', 'Vihren'])) {
            $logo = asset('images/vihren.png');
        } elseif ($local && ! empty($local->logo)) {
            $logo = asset('storage/'.ltrim($local->logo, '/'));
        } else {
            $logo = $item['team']['logo'] ?? null;
        }

        if ($logo && ! str_starts_with($logo, 'http')) {
            $logo = asset('storage/'.ltrim($logo, '/'));
        }

        $isCska = str_contains(mb_strtolower($translated), 'цска');

        return [
            'rank' => $item['rank'],
            'points' => $item['points'],
            'matches' => $item['matches'],
            'won' => $item['won'],
            'drawn' => $item['drawn'],
            'lost' => $item['lost'],
            'goal_diff' => $item['goal_diff'],
            'goals_scored' => $item['goals_scored'],
            'goals_conceded' => $item['goals_conceded'],
            'form' => $item['form'] ?? [],

            'team_id' => $externalId,
            'name' => $translated,
            'bg_name' => $translated,
            'logo' => $logo,
            'stadium' => $local->stadium ?? ($item['team']['stadium'] ?? null),
            'manager' => $local->manager ?? null,
            'is_cska' => $isCska,
        ];
    }
}
