<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterWidgetItem extends Model
{
    protected $fillable = [
        'footer_widget_id',
        'text',
        'link',
        'sort_order'
    ];

    public function widget()
    {
        return $this->belongsTo(FooterWidget::class);
    }
}