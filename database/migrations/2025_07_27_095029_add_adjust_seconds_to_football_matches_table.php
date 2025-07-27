<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_add_adjust_seconds_to_football_matches_table.php
    public function up()
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->integer('adjust_seconds')->default(0);
        });
    }

    public function down()
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->dropColumn('adjust_seconds');
        });
    }
};
