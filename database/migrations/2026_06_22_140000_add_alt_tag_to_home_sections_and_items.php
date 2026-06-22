<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->string('alt_tag')->nullable()->after('image');
        });

        Schema::table('home_section_items', function (Blueprint $table) {
            $table->string('alt_tag')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->dropColumn('alt_tag');
        });

        Schema::table('home_section_items', function (Blueprint $table) {
            $table->dropColumn('alt_tag');
        });
    }
};
