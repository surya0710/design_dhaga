<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('awb_code')->nullable()->after('shiprocket_shipment_id');
            $table->decimal('package_length', 8, 2)->nullable()->after('delivery_eta');
            $table->decimal('package_breadth', 8, 2)->nullable()->after('package_length');
            $table->decimal('package_height', 8, 2)->nullable()->after('package_breadth');
            $table->decimal('package_weight', 8, 2)->nullable()->after('package_height');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'awb_code',
                'package_length',
                'package_breadth',
                'package_height',
                'package_weight',
            ]);
        });
    }
};