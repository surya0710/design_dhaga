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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_type')->nullable();           // regular / express
            $table->decimal('delivery_charge', 10, 2)->default(0); // shipping amount
            $table->string('delivery_eta')->nullable();            // "3-5 days"
            $table->string('courier_name')->nullable();            // Delhivery / Bluedart
            $table->string('delivery_label')->nullable();          // economical / fastest
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
