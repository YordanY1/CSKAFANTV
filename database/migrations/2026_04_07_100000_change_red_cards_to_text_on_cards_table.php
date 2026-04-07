<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->text('direct_red_note')->nullable()->after('red_cards');
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('red_cards');
        });
    }

    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->unsignedTinyInteger('red_cards')->default(0)->after('yellow_cards');
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('direct_red_note');
        });
    }
};
