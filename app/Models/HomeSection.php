<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'subtitle',
        'body',
        'image',
        'button_text',
        'button_url',
        'button_target',
        'layout',
        'bg_class',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(HomeSectionItem::class)->orderBy('sort_order')->orderBy('id');
    }
}
