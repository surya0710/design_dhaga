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
        if (!Schema::hasColumn('contacts', 'instagram')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('instagram')->nullable()->after('design');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('contacts', 'instagram')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('instagram');
            });
        }
    }
};
