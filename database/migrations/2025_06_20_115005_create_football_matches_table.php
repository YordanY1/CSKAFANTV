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
        Schema::create('football_matches', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->cascadeOnDelete();
            $table->dateTime('match_datetime');
            $table->string('stadium')->nullable();
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->string('youtube_url')->nullable();

            $table->text('voice_of_the_fan_embed')->nullable();        // 🎤 Гласът на ФЕНА
            $table->text('before_match_embed')->nullable();            // ⏱️ Преди мача
            $table->text('talk_show_embed')->nullable();               // 🎙️ CSKA FAN TV TALK SHOW
            $table->text('member_stream_embed')->nullable();           // 🔒 Специални стриймове за членове
            $table->text('celebrity_fans_embed')->nullable();          // ⭐ Именити червени фенове гостуват
            $table->text('legends_speak_embed')->nullable();           // 🧓 Легендите говорят
            $table->text('red_glory_embed')->nullable();               // 🏆 Червена слава
            $table->text('cska_future_embed')->nullable();             // 🌱 Бъдещето на ЦСКА
            $table->text('cska_kids_embed')->nullable();               // 👶 Децата на ЦСКА
            $table->text('guest_answers_embed')->nullable();           // 📣 Отговори от гости
            $table->text('preseason_training_embed')->nullable();      // 🏋️ Предсезонна подготовка

            $table->boolean('is_finished')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_matches');
    }
};
