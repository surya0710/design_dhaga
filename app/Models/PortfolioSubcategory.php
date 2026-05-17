<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_category_id',
        'name',
        'slug',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id');
    }

    public function galleries()
    {
        return $this->hasMany(PortfolioGallery::class)->orderBy('sort_order')->latest();
    }
}
