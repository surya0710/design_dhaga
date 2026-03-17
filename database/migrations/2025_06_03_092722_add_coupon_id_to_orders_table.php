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
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null')->after('coupon_code');
            // Adding coupon_id to orders table
            // It is nullable, meaning an order may not always have a coupon applied
            // If the coupon is deleted, it will set the coupon_id to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            // Dropping the foreign key constraint for coupon_id
            // This is necessary to remove the column safely
            $table->dropColumn('coupon_id');
            // Finally, we drop the coupon_id column from the orders table
        });
    }
};
