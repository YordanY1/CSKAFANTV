<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->unsignedTinyInteger('obs_home_score')->nullable();
            $table->unsignedTinyInteger('obs_away_score')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->dropColumn(['obs_home_score', 'obs_away_score']);
        });
    }
};
