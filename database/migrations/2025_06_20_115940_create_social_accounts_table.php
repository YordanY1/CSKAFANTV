<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // google, facebook
            $table->string('provider_id'); // id from provider
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('social_accounts');
    }
};

