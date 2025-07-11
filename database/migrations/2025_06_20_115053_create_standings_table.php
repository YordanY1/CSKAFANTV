<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('played')->default(0);
            $table->unsignedTinyInteger('wins')->default(0);
            $table->unsignedTinyInteger('draws')->default(0);
            $table->unsignedTinyInteger('losses')->default(0);

            $table->unsignedTinyInteger('points')->default(0);
            $table->unsignedSmallInteger('goals_scored')->default(0);
            $table->unsignedSmallInteger('goals_conceded')->default(0);

            $table->unsignedTinyInteger('manual_rank')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standings');
    }
};
