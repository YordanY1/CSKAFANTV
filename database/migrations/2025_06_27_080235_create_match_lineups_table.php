<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('match_lineups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('football_match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_starter')->default(true);
            $table->foreignId('replaces_player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->unsignedTinyInteger('minute_entered')->nullable();
            $table->unsignedTinyInteger('minute_substituted')->nullable();
            $table->timestamps();

            $table->unique(['football_match_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_lineups');
    }
};
