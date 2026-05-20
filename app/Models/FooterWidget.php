<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterWidget extends Model
{
    protected $fillable = [
        'title',
        'sort_order'
    ];

    public function items()
    {
        return $this->hasMany(FooterWidgetItem::class)
            ->orderBy('sort_order');
    }
}