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

    /**
     * Първа лига
     */
    public function getStandingsWithTeams(int $competitionId = 71): array
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

        return collect(data_get($response, 'data.stages', []))
            ->flatMap(
                fn($stage) =>
                collect($stage['groups'] ?? [])->flatMap(fn($group) => $group['standings'] ?? [])
            )
            ->map(fn($item) => $this->mapTeamData($item, $localTeams))
            ->toArray();
    }

    /**
     * Втора лига
     */
    public function getSecondLeagueStandings(): array
    {
        $response = Http::get("{$this->baseUrl}/competitions/table.json", [
            'competition_id' => 140,
            'key'            => $this->key,
            'secret'         => $this->secret,
            'lang'           => 'bg',
            'include_form'   => 1,
        ])->json();

        if (!data_get($response, 'success')) {
            return [];
        }

        $localTeams = DB::table('teams')
            ->select('id', 'name', 'logo', 'external_id', 'stadium', 'manager')
            ->get()
            ->keyBy('external_id');

        return collect(data_get($response, 'data.stages', []))
            ->flatMap(
                fn($stage) =>
                collect($stage['groups'] ?? [])->flatMap(fn($group) => $group['standings'] ?? [])
            )
            ->map(fn($item) => $this->mapTeamData($item, $localTeams))
            ->toArray();
    }

    /**
     * Унифицирана логика за мапване на отбори
     */
    protected function mapTeamData(array $item, $localTeams): array
    {
        $translations = [
            'PFC CSKA-Sofia'              => 'ЦСКА',
            'CSKA Sofia II'               => 'ЦСКА II',
            'CSKA 1948'                   => 'ЦСКА 1948',
            'Vitosha Bistritsa'           => 'Бистрица',
            'Levski Sofia'                => 'Левски',
            'Lokomotiv Plovdiv'           => 'Локо Пд',
            'Botev Plovdiv'               => 'Ботев Пд',
            'OFC Botev Vratsa'            => 'Ботев Враца',
            'Ludogorets Razgrad'          => 'Лудогорец',
            'Cherno More Varna'           => 'Черно море',
            'Beroe'                       => 'Берое',
            'PFC Lokomotiv Sofia 1929'    => 'Локо Сф',
            'PFC Spartak Varna'           => 'Спартак Вн',
            'FK Arda Kurdzhali'           => 'Арда',
            'Slavia Sofia'                => 'Славия',
            'Septemvri Sofia'             => 'Септември',
            'PFC Dobrudzha Dobrich'       => 'Добруджа',
            'Montana'                     => 'Монтана',
        ];

        $externalId = $item['team']['id'] ?? null;
        $local = $externalId ? $localTeams->get($externalId) : null;
        $originalName = $item['team']['name'] ?? null;

        $translated = $local->name ?? ($translations[$originalName] ?? $originalName);

        if (in_array($originalName, ['CSKA 1948', 'Vitosha Bistritsa'])) {
            $translated = 'Бистрица';
        }

        $isCska = str_contains(mb_strtolower($translated), 'цска');

        $logo = $local->logo ?? ($item['team']['logo'] ?? null);

        if ($logo && !str_starts_with($logo, 'http')) {
            $logo = asset('storage/' . ltrim($logo, '/'));
        }

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
