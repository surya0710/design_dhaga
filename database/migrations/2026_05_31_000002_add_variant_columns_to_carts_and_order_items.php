<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
            $table->string('size')->nullable()->after('price');
            $table->string('fabric_type')->nullable()->after('size');
            $table->string('sku')->nullable()->after('fabric_type');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');
            $table->string('sku')->nullable()->after('product_image');
            $table->string('size')->nullable()->after('sku');
            $table->string('fabric_type')->nullable()->after('size');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_variant_id', 'sku', 'size', 'fabric_type']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
            $table->dropColumn(['size', 'fabric_type', 'sku']);
        });
    }
};
