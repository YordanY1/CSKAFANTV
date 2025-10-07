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

        // Взимаме локалните отбори (тук приемам, че ще добавиш external_id поле в teams таблицата)
        $localTeams = DB::table('teams')
            ->select('id', 'name', 'logo', 'stadium', 'manager', 'external_id')
            ->get()
            ->keyBy('external_id');

        $standings = collect(data_get($response, 'data.stages', []))
            ->flatMap(
                fn($stage) => collect($stage['groups'] ?? [])
                    ->flatMap(fn($group) => $group['standings'] ?? [])
            )
            ->map(function ($item) use ($localTeams) {
                $externalId = $item['team']['id'] ?? null;

                // търсим отбор по external_id
                $local = $externalId ? $localTeams->get($externalId) : null;

                $name = $item['team']['name'] ?? null;

                // специално за ЦСКА
                if ($name === 'PFC CSKA-Sofia') {
                    $name = 'ЦСКА';
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
                    'name'           => $local->name ?? $name,
                    'logo'           => $local->logo ?? ($item['team']['logo'] ?? null),
                    'stadium'        => $local->stadium ?? ($item['team']['stadium'] ?? null),
                    'manager'        => $local->manager ?? null,

                    'bg_name'        => $local->name ?? $name,
                    'is_cska'        => str_contains(mb_strtolower($local->name ?? $name), 'цска'),
                ];
            })
            ->toArray();

        return $standings;
    }
}
