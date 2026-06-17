<?php

use App\Support\Season;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Re-derive the season for every match using the current rule (June cutover,
     * folded to the first season 2025-2026). This fixes any values written by an
     * earlier backfill and establishes a consistent baseline. It only writes the
     * derived `season` value and never touches any other column, so no data is
     * lost. The query builder is used so the model's "saved" hook is not fired.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('football_matches', 'season')) {
            Schema::table('football_matches', function (Blueprint $table) {
                $table->string('season')->nullable()->after('match_datetime')->index();
            });
        }

        DB::table('football_matches')
            ->select('id', 'match_datetime')
            ->orderBy('id')
            ->chunkById(500, function ($matches) {
                foreach ($matches as $match) {
                    if (empty($match->match_datetime)) {
                        continue;
                    }

                    DB::table('football_matches')
                        ->where('id', $match->id)
                        ->update(['season' => Season::fromDate(Carbon::parse($match->match_datetime))]);
                }
            });
    }

    public function down(): void
    {
        // Season values are derived from match dates; nothing to revert.
    }
};
