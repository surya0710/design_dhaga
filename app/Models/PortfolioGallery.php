<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_category_id',
        'portfolio_subcategory_id',
        'title',
        'image',
        'alt_text',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(PortfolioSubcategory::class, 'portfolio_subcategory_id');
    }
}
