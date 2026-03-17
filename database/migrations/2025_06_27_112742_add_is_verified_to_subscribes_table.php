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
        Schema::table('subscribes', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('email');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            $table->string('verification_token', 64)->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribes', function (Blueprint $table) {
            $table->dropColumn('is_verified');
            $table->dropColumn('verified_at');
            $table->dropColumn('verification_token');
        });
    }
};
