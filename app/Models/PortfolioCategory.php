<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function subcategories()
    {
        return $this->hasMany(PortfolioSubcategory::class)->orderBy('sort_order')->orderBy('name');
    }

    public function galleries()
    {
        return $this->hasMany(PortfolioGallery::class)->orderBy('sort_order')->latest();
    }
}
