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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // single unit price
            $table->decimal('total', 10, 2); // total price for the quantity
            $table->string('status')->default('pending'); // e.g., pending, completed, refunded
            // This status can be used to track the state of each item in the order
            $table->string('product_sku')->nullable(); // SKU of the product, if applicable
            $table->string('product_category')->nullable(); // Category of the product, if applicable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
