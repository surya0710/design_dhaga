<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('slug');                        // product lookup
            $table->index(['category_id', 'status']);     // related products query
            $table->index('featured');                    // if you query featured products
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['product_id', 'approved']);    // approvedReviews relation
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->index(['product_id', 'type']);        // galleryImages / artisanImages
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
