<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sliders extends Model
{
    protected $table = 'sliders';

    protected $fillable = [
        'image',
        'image_alt',
        'heading',
        'description',
        'button_text',
        'button_link',
        'order',
        'active_status',
        'target',
        'text_location',
        'text_color'
    ];

    protected $casts = [
        'active_status' => 'boolean',
    ];

    /**
     * Scope: Only active sliders
     */
    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    /**
     * Scope: Ordered sliders
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}