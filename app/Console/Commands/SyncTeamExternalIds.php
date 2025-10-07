<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTeamExternalIds extends Command
{
    protected $signature = 'teams:sync-external-ids';
    protected $description = 'Синхронизира external_id за отборите в таблицата teams';

    public function handle()
    {
        $map = [
            'ЦСКА' => 1395,
            'Бистрица' => 2568, 
            'Левски' => 918,
            'Лудогорец' => 155,
            'Ботев Пд' => 1220,
            'Черно Море' => 1111,
            'Локо Пд' => 732,
            'Локо Сф' => 2451,
            'Ботев Враца' => 2006,
            'Берое' => 1221,
            'Арда' => 2536,
            'Славия' => 446,
            'Спартак Вн' => 2594,
            'Септември' => 447,
            'Добруджа' => 2450,
            'Монтана' => 2463,
        ];

        $updated = 0;

        foreach ($map as $name => $externalId) {
            $affected = DB::table('teams')
                ->where('name', $name)
                ->update(['external_id' => $externalId]);

            if ($affected) {
                $this->info("✅ Обновен: {$name} ({$externalId})");
                $updated++;
            } else {
                $this->warn("⚠️ Пропуснат: {$name} (няма такъв запис)");
            }
        }

        $this->line("🎯 Обновени {$updated} отбора успешно.");
        return 0;
    }
}
