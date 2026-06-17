<?php

use App\Support\Season;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->string('season')->nullable()->after('match_datetime')->index();
        });

        // Backfill every existing match from its date. We use the query builder
        // (not the Eloquent model) on purpose so the model's "saved" hook — which
        // dispatches predictions:calculate-points — is NOT triggered during the
        // migration. This is purely additive and does not touch any other column.
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
        Schema::table('football_matches', function (Blueprint $table) {
            $table->dropIndex(['season']);
            $table->dropColumn('season');
        });
    }
};
