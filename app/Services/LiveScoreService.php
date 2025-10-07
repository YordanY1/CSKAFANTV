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

        $localTeams = DB::table('teams')
            ->select('id', 'name', 'logo', 'stadium', 'manager', 'external_id')
            ->get()
            ->keyBy('external_id');

        return collect(data_get($response, 'data.stages', []))
            ->flatMap(fn($stage) => collect($stage['groups'] ?? [])->flatMap(fn($group) => $group['standings'] ?? []))
            ->map(fn($item) => $this->mapTeamData($item, $localTeams))
            ->toArray();
    }

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
            ->flatMap(fn($stage) => collect($stage['groups'] ?? [])->flatMap(fn($group) => $group['standings'] ?? []))
            ->map(fn($item) => $this->mapTeamData($item, $localTeams))
            ->toArray();
    }

    /**
     * 🧠 Централизирана логика за превод и обогатяване на данни за отбор
     */
    protected function mapTeamData(array $item, $localTeams): array
    {
        // Локални преводи EN → BG
        $translations = [
            'Dunav Ruse'                   => 'Дунав Русе',
            'Fratria'                      => 'Фратрия',
            'Yantra'                       => 'Янтра',
            'Vihren Sandanski'             => 'Вихрен Сандански',
            'Pirin Blagoevgrad'            => 'Пирин Благоевград',
            'Lokomotiv Gorna Oryahovitsa'  => 'Локомотив Горна Оряховица',
            'CSKA Sofia II'                => 'ЦСКА II',
            'Minyor Pernik'                => 'Миньор Перник',
            'Hebar Pazardzhik'             => 'Хебър Пазарджик',
            'Chernomorets Burgas'          => 'Черноморец Бургас',
            'Sevlievo'                     => 'Севлиево',
            'Ludogorets II'                => 'Лудогорец II',
            'Spartak Pleven'               => 'Спартак Плевен',
            'Etar'                         => 'Етър',
            'Marek'                        => 'Марек',
            'Sportist Svoge'               => 'Спортист Своге',
            'Belasitsa Petrich'            => 'Беласица Петрич',
        ];

        $externalId = $item['team']['id'] ?? null;
        $local = $externalId ? $localTeams->get($externalId) : null;
        $originalName = $item['team']['name'] ?? null;

        if ($local && !empty($local->name)) {
            $translated = $local->name;
        } else {
            $translated = $translations[$originalName] ?? $originalName;
        }

        if ($local && str_contains(mb_strtolower($local->name), 'цска')) {
            $translated = $local->name;
        }

        if ($local && !empty($local->logo)) {
            $logo = $local->logo;
        } else {
            $logo = $item['team']['logo'] ?? null;
        }

        if (in_array($originalName, ['Vihren Sandanski', 'Vihren'])) {
            $logo = asset('images/vihren.png');
        }


        if ($logo && !str_starts_with($logo, 'http') && !str_starts_with($logo, asset(''))) {
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
            'is_cska'        => str_contains(mb_strtolower($translated), 'цска'),
        ];
    }
}
