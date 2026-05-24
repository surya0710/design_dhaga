<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $fillable = [
        'heading',
        'description',
        'signature',
        'image',
        'value_items',
    ];

    protected $casts = [
        'value_items' => 'array',
    ];

    public static function defaultValueItems(): array
    {
        return [
            [
                'icon' => 'frontend_assets/images/icons/TimeLess icon.svg',
                'alt' => 'Timeless',
                'title' => 'सदाबहार | TIMELESS',
                'description' => "Design Dhaga creates designs that never fade with time — whether it's a hand-painted saree, dupatta, kurta, or a digital logo crafted for a brand. Our work is rooted in minimal, meaningful aesthetics and exceptional quality, ensuring every piece — fabric or graphic — stays relevant, elegant, and cherished forever.",
            ],
            [
                'icon' => 'frontend_assets/images/icons/Honest icon.svg',
                'alt' => 'Honest',
                'title' => 'सच | HONEST',
                'description' => 'Honesty is woven into everything we create. From the authenticity of hand-painted fabrics to transparent pricing and clear communication in our graphic design services, we promise no hidden surprises — only genuine creativity and fair value you can trust.',
            ],
            [
                'icon' => 'frontend_assets/images/icons/Easy Icon.svg',
                'alt' => 'Easy',
                'title' => 'सरल | EASY',
                'description' => "At Design Dhaga, simplicity is our strength. We make the process easy — whether you're customizing a hand-painted outfit or building your visual identity through graphic design. Smooth workflow, clear guidance, and effortless experience — just like the comfort of a warm cup of chai.",
            ],
        ];
    }

    public function getDisplayValueItemsAttribute(): array
    {
        $items = is_array($this->value_items) ? $this->value_items : [];

        return count($items) ? $items : self::defaultValueItems();
    }
}
