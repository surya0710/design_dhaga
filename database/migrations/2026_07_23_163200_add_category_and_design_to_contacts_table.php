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
        if (!Schema::hasColumn('contacts', 'category')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('category')->nullable()->after('mobile');
            });
        }

        if (!Schema::hasColumn('contacts', 'design')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('design')->nullable()->after('message');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('contacts', 'design')) {
                $table->dropColumn('design');
            }
        });
    }
};
