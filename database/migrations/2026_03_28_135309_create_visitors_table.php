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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();

            $table->text('visitor_id');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();

            $table->string('referrer')->nullable();

            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();

            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();

            $table->timestamp('visited_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
