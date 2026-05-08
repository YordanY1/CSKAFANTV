<?php

namespace App\Services;

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
        $response = Http::get("{$this->baseUrl}/competitions/table.json", [
            'competition_id' => $competitionId,
            'key'            => $this->key,
            'secret'         => $this->secret,
            'lang'           => 'bg',
            'include_form'   => 1,
        ])->json();

        if (!data_get($response, 'success')) {
            return [];
        }

        $localTeams = DB::table('teams')
            ->select('id', 'name', 'logo', 'stadium', 'manager', 'external_id')
            ->get()
            ->keyBy('external_id');

        $entries = collect(data_get($response, 'data.stages', []))
            ->flatMap(
                fn($stage) =>
                collect($stage['groups'] ?? [])->flatMap(fn($group) => $group['standings'] ?? [])
            );

        // First League returns multiple stages (regular season + championship /
        // 5-8 / relegation playoff groups). Each team appears once per stage,
        // so dedupe by team id and keep the entry with the most matches played
        // — that's the live playoff row, not the stale regular-season one.
        $deduped = $entries
            ->groupBy(fn($item) => $item['team']['id'] ?? null)
            ->map(fn($rows) => $rows->sortByDesc(fn($r) => (int) ($r['matches'] ?? 0))->first())
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

    protected function mapTeamData(array $item, $localTeams): array
    {
        $translations = [
            'Dunav Ruse'                  => 'Дунав Русе',
            'Fratria'                     => 'Фратрия',
            'Yantra'                      => 'Янтра',
            'Vihren Sandanski'            => 'Вихрен Сандански',
            'Pirin Blagoevgrad'           => 'Пирин Благоевград',
            'Lokomotiv Gorna Oryahovitsa' => 'Локомотив Горна Оряховица',
            'CSKA Sofia II'               => 'ЦСКА II',
            'Minyor Pernik'               => 'Миньор Перник',
            'Hebar Pazardzhik'            => 'Хебър Пазарджик',
            'Chernomorets Burgas'         => 'Черноморец Бургас',
            'Sevlievo'                    => 'Севлиево',
            'Ludogorets II'               => 'Лудогорец II',
            'Spartak Pleven'              => 'Спартак Плевен',
            'Etar'                        => 'Етър',
            'Marek'                       => 'Марек',
            'Sportist Svoge'              => 'Спортист Своге',
            'Belasitsa Petrich'           => 'Беласица Петрич',
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

            if ($cskaRecord && !empty($cskaRecord->logo)) {
                $logo = asset('storage/' . ltrim($cskaRecord->logo, '/'));
            } else {
                $logo = asset('images/cska.png');
            }
        } elseif (in_array($originalName, ['Vihren Sandanski', 'Vihren'])) {
            $logo = asset('images/vihren.png');
        } elseif ($local && !empty($local->logo)) {
            $logo = asset('storage/' . ltrim($local->logo, '/'));
        } else {
            $logo = $item['team']['logo'] ?? null;
        }

        if ($logo && !str_starts_with($logo, 'http')) {
            $logo = asset('storage/' . ltrim($logo, '/'));
        }

        $isCska = str_contains(mb_strtolower($translated), 'цска');

        return [
            'rank'           => $item['rank'],
            'points'         => $item['points'],
            'matches'        => $item['matches'],
            'won'            => $item['won'],
            'drawn'          => $item['drawn'],
            'lost'           => $item['lost'],
            'goal_diff'      => $item['goal_diff'],
            'goals_scored'   => $item['goals_scored'],
            'goals_conceded' => $item['goals_conceded'],
            'form'           => $item['form'] ?? [],

            'team_id'        => $externalId,
            'name'           => $translated,
            'bg_name'        => $translated,
            'logo'           => $logo,
            'stadium'        => $local->stadium ?? ($item['team']['stadium'] ?? null),
            'manager'        => $local->manager ?? null,
            'is_cska'        => $isCska,
        ];
    }
}
