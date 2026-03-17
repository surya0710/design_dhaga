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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('email');
            $table->string('mobile');
            $table->string('country')->default('India'); // Default country set to India
            $table->string('state');
            $table->string('city');
            $table->string('street', 500);
            $table->string('pincode');
            $table->string('landmark', 500)->nullable(); // Optional landmark for better address clarity
            $table->string('address_type')->default('home'); // home, office, other
            $table->text('notes')->nullable(); // Additional notes for the order
            $table->string('coupon_code')->nullable(); // Optional coupon code
            $table->decimal('coupon_discount', 8, 2)->default(0); // Discount applied from coupon
            $table->decimal('delivery_charge', 8, 2)->default(0); // Discount applied from coupon
            $table->enum('payment_method', ['cod', 'online']);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, shipped, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
