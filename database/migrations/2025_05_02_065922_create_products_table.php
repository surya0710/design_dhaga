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
        Schema::create('products', function (Blueprint $table) {

            $table->id();
        
            $table->string('name');
            $table->string('slug')->unique();
        
            $table->string('short_description')->nullable();
            $table->longText('description');
        
            $table->decimal('regular_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
        
            $table->string('sku')->unique();
        
            $table->integer('quantity')->default(0);
        
            $table->boolean('featured')->default(false);
        
            $table->boolean('stock_status')->default(true); // true = in stock
        
            $table->string('image')->nullable();
        
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        
            $table->decimal('weight', 8, 3)->nullable();
        
            $table->string('dimension')->nullable();
        
            $table->string('color')->nullable();
        
            $table->string('type')->nullable();
        
            $table->text('tags')->nullable();
        
            $table->boolean('status')->default(true);
        
            $table->string('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
        
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
