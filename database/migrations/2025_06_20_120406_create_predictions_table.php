<?php

// database/migrations/xxxx_xx_xx_create_predictions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('football_match_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('home_score_prediction');
            $table->unsignedTinyInteger('away_score_prediction');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
