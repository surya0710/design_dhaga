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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Logos
            $table->string('logo')->nullable();
            $table->string('dark_logo')->nullable();
            $table->string('favicon')->nullable();

            // Store Details
            $table->string('store_name')->nullable();
            $table->text('office_address')->nullable();
            $table->text('store_address')->nullable();

            // Contact Details
            $table->string('support_email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('whatsapp_number')->nullable();

            // Business Timings
            $table->string('working_days')->nullable();
            $table->string('opening_time')->nullable();
            $table->string('closing_time')->nullable();

            // Social Media Links
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();

            // SEO & Extra
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};