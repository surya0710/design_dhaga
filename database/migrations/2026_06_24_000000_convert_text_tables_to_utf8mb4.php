<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cache')) {
            DB::statement('ALTER TABLE `cache` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }

        if (Schema::hasTable('cache_locks')) {
            DB::statement('ALTER TABLE `cache_locks` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }

        if (Schema::hasTable('home_sections')) {
            DB::statement('ALTER TABLE `home_sections` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }

        if (Schema::hasTable('home_section_items')) {
            DB::statement('ALTER TABLE `home_section_items` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    public function down(): void
    {
        //
    }
};
