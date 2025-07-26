<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicatePredictions extends Command
{
    protected $signature = 'predictions:cleanup-duplicates';
    protected $description = 'Премахва стари дублирани прогнози (оставя само последната за всеки мач и потребител)';

    public function handle()
    {
        $this->info("Започвам почистване на дубли...");

        $duplicates = DB::table('predictions')
            ->select('user_id', 'football_match_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id', 'football_match_id')
            ->having('total', '>', 1)
            ->get();

        $totalDeleted = 0;

        foreach ($duplicates as $dup) {
            $all = DB::table('predictions')
                ->where('user_id', $dup->user_id)
                ->where('football_match_id', $dup->football_match_id)
                ->orderByDesc('created_at')
                ->get();

            $toKeep = $all->first();
            $toDelete = $all->slice(1);

            foreach ($toDelete as $pred) {
                DB::table('prediction_results')->where('prediction_id', $pred->id)->delete();
                DB::table('predictions')->where('id', $pred->id)->delete();
                $totalDeleted++;
            }
        }

        $this->info("Премахнати са $totalDeleted дублирани прогнози.");
    }
}
