<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name','slug','short_description','description',
        'regular_price','sale_price','sku','stock_status',
        'featured','quantity','image','category_id',
        'type','weight','dimension','color','tags',
        'hand_painted_details','care_instructions',
        'manufacturing_details','square_banner',
        'square_banner_title','square_banner_description',
        'artisan_heading','meta_title','meta_keywords','meta_description',
    ];

    // ✅ Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')
            ->select('id','name','slug','parent_id');
    }

    // ✅ Images
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')
            ->select('id','product_id','image','type');
    }

    // ✅ Attributes
    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id')
            ->select('id','product_id','key','value');
    }

    // ✅ Gallery
    public function galleryImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id')
            ->where('type', 'gallery')
            ->select('id','product_id','image')
            ->orderBy('id');
    }

    // ✅ Artisan
    public function artisanImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id')
            ->where('type', 'artisan')
            ->select('id','product_id','image','title','description')
            ->orderBy('id');
    }

    // ✅ Reviews
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    // ✅ Icons
    public function icons()
    {
        return $this->hasMany(ProductIcon::class)
            ->select('id','product_id','image','text');
    }
}