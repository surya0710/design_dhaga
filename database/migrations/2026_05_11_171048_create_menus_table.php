<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // "Main Menu", "Footer Menu"
            $table->string('slug')->unique();  // "main-menu", "footer-menu"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()
                  ->constrained('menu_items')->nullOnDelete();
            $table->string('label');                    // "Home", "About Us"
            $table->string('url')->nullable();          // static URL
            $table->string('route_name')->nullable();   // named route e.g. "contact"
            $table->json('route_params')->nullable();   // {"id": 5}
            $table->string('icon')->nullable();         // "fas fa-home"
            $table->string('target')->default('_self');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};