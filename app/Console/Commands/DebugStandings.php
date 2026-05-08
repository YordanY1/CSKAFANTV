<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DebugStandings extends Command
{
    protected $signature = 'debug:standings {competition=71}';
    protected $description = 'Дъмпва структурата на LiveScore standings API за дебъг на дубликати';

    public function handle(): int
    {
        $competitionId = (int) $this->argument('competition');

        $response = Http::get('https://livescore-api.com/api-client/competitions/table.json', [
            'competition_id' => $competitionId,
            'key'            => config('services.livescore.key'),
            'secret'         => config('services.livescore.secret'),
            'lang'           => 'bg',
            'include_form'   => 1,
        ])->json();

        if (!data_get($response, 'success')) {
            $this->error('API върна success=false');
            $this->line(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return 1;
        }

        $stages = data_get($response, 'data.stages', []);

        $this->line("Competition: {$competitionId}");
        $this->line('Stages count: ' . count($stages));
        $this->newLine();

        $teamCounts = [];

        foreach ($stages as $sIdx => $stage) {
            $stageName = $stage['name'] ?? "stage[{$sIdx}]";
            $stageId   = $stage['stage_id'] ?? $stage['id'] ?? '?';
            $groups    = $stage['groups'] ?? [];
            $this->line("─ STAGE {$sIdx}: \"{$stageName}\" (id={$stageId}), groups=" . count($groups));

            foreach ($groups as $gIdx => $group) {
                $groupName = $group['name'] ?? "group[{$gIdx}]";
                $standings = $group['standings'] ?? [];
                $this->line("   └ GROUP {$gIdx}: \"{$groupName}\", teams=" . count($standings));

                foreach ($standings as $row) {
                    $teamId   = $row['team']['id']   ?? null;
                    $teamName = $row['team']['name'] ?? '?';
                    $rank     = $row['rank']         ?? '?';
                    $matches  = $row['matches']      ?? '?';
                    $points   = $row['points']       ?? '?';

                    $this->line("       #{$rank}  {$teamName} (id={$teamId})  M={$matches}  P={$points}");

                    if ($teamId !== null) {
                        $teamCounts[$teamId] = ($teamCounts[$teamId] ?? 0) + 1;
                    }
                }
            }
        }

        $this->newLine();
        $duplicates = array_filter($teamCounts, fn($c) => $c > 1);

        if (empty($duplicates)) {
            $this->info('✅ Няма дубликати — всеки team_id се среща веднъж.');
        } else {
            $this->warn('⚠️ Дубликати (team_id => брой появи):');
            foreach ($duplicates as $id => $count) {
                $this->line("   {$id} × {$count}");
            }
        }

        return 0;
    }
}
