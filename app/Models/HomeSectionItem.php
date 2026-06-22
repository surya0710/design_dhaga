<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_section_id',
        'title',
        'subtitle',
        'body',
        'image',
        'alt_tag',
        'link_text',
        'link_url',
        'icon',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(HomeSection::class, 'home_section_id');
    }
}
