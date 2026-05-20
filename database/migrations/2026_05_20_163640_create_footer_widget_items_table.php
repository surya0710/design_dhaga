<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_widget_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('footer_widget_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('text');

            $table->string('link')->nullable();

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_widget_items');
    }
};