<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_add_category_slug_to_videos_table.php
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('category_slug')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('category_slug');
        });
    }
};
