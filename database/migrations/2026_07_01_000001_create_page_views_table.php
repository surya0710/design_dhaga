<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path', 500);
            $table->text('url')->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('viewed_at');

            $table->index('visitor_id');
            $table->index('viewed_at');
            $table->index('path');
            $table->index('country');
            $table->index('utm_source');
            $table->index(['viewed_at', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
