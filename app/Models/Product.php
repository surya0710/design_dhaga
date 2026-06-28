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
        'artisan_heading','meta_title','meta_keywords','meta_description', 'status'
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

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)
            ->orderBy('size')
            ->orderBy('fabric_type');
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->orderBy('price');
    }

    public function getMinVariantPriceAttribute(): ?float
    {
        if ($this->relationLoaded('activeVariants')) {
            return $this->activeVariants->min('price') !== null
                ? (float) $this->activeVariants->min('price')
                : null;
        }

        if ($this->relationLoaded('variants')) {
            $price = $this->variants->where('is_active', true)->min('price');
            return $price !== null ? (float) $price : null;
        }

        $price = $this->activeVariants()->min('price');
        return $price !== null ? (float) $price : null;
    }

    public function getDisplayPriceAttribute(): float
    {
        return $this->min_variant_price ?? (float) ($this->sale_price ?: $this->regular_price);
    }

    public function getHasActiveVariantsAttribute(): bool
    {
        if ($this->relationLoaded('activeVariants')) {
            return $this->activeVariants->isNotEmpty();
        }

        if ($this->relationLoaded('variants')) {
            return $this->variants->where('is_active', true)->isNotEmpty();
        }

        return $this->activeVariants()->exists();
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

    /**
     * Convert stored product weight to kilograms for Shiprocket.
     * Admin expects kg (e.g. 0.500), but legacy rows may store grams (e.g. 500).
     */
    public static function normalizeWeightToKg(null|float|string $weight, float $minimum = 0.5): float
    {
        $weight = (float) ($weight ?? 0);

        if ($weight <= 0) {
            return max($minimum, 0.5);
        }

        if ($weight >= 100 || ($weight >= 10 && abs($weight - round($weight)) < 0.0001)) {
            $weight = $weight / 1000;
        }

        return max($weight, 0.5);
    }

    public function getShiprocketWeightKgAttribute(): float
    {
        return self::normalizeWeightToKg($this->weight);
    }
}
