<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'sku',
        'stock_status',
        'featured',
        'quantity',
        'image',
        'category_id',
        'type',
        'weight',
        'dimension',
        'color',
        'tags',
        'hand_painted_details',
        'care_instructions',
        'manufacturing_details',
        'square_banner',
        'square_banner_title',
        'square_banner_description',
        'artisan_heading'
    ];

    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')
            ->select('id','name','slug');
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT IMAGES
    |--------------------------------------------------------------------------
    */

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | GET ATTRIBUTE VALUE
    |--------------------------------------------------------------------------
    */

    public function getProductAttributeValue($key)
    {
        return $this->productAttributes()
            ->where('key', $key)
            ->value('value');
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT BY SLUG
    |--------------------------------------------------------------------------
    */

    public static function getSingleSlug($slug)
    {
        return self::where('slug', $slug)
            ->where('status', 1)
            ->with([
                'category:id,name,slug',
                'images:id,product_id,image',
                'productAttributes:id,product_id,key,value'
            ])
            ->first();
    }

    public function artisanImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id')
                    ->where('type', 'artisan')
                    ->orderBy('id');
    }

    public function galleryImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->where('type', 'gallery')->orderBy('id');
    }
}