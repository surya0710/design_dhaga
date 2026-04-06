<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // User
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Saved address reference
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();

            // Customer snapshot
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);

            // Address snapshot
            $table->string('country')->default('India');
            $table->string('state');
            $table->string('city');
            $table->string('pincode', 20);
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
            $table->string('landmark')->nullable();
            $table->enum('address_type', ['home', 'work', 'other'])->default('home');

            // Notes
            $table->text('notes')->nullable();

            // Pricing
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Coupon
            $table->string('coupon_code')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();

            // Payment
            $table->string('payment_method')->default('razorpay');
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();

            // Order lifecycle
            $table->string('order_status')->default('pending'); // pending, confirmed, packed, shipped, delivered, cancelled
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};