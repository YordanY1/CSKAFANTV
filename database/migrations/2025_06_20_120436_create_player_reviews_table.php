<?php

// database/migrations/xxxx_xx_xx_create_player_reviews_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('player_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('match_id')->constrained('football_matches')->cascadeOnDelete();
            $table->text('review')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->timestamps();

            $table->unique(['user_id', 'player_id', 'match_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_reviews');
    }
};
