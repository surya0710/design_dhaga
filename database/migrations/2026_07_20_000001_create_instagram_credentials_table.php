<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instagram_credentials', function (Blueprint $table) {
            $table->id();
            $table->text('access_token');
            $table->string('user_id')->nullable();
            $table->string('page_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_refreshed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_credentials');
    }
};
