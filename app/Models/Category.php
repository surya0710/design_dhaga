<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'parent_id',
        'status',
        'meta_title',
        'meta_keywords',
        'meta_description'
    ];
    
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id')
            ->select('id','name','slug','category_id','image');
    }

    public static function getSingleSlug($slug)
    {
        return self::where('slug', $slug)
            ->where('status', 1)
            ->first();
    }
}