<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $sectionId = DB::table('home_sections')->insertGetId([
            'key' => 'instagram_feed',
            'title' => 'Design Dhaga',
            'subtitle' => 'Follow and share your feedback with us on Instagram',
            'body' => "@designdhaga\nHandmade, hand-painted ethnic wear and designer clothing for women, men, kids and celebrities.",
            'image' => 'frontend_assets/images/logo/logo.svg',
            'button_text' => 'Follow on Instagram',
            'button_url' => 'https://www.instagram.com/design.dhaga',
            'button_target' => '_blank',
            'layout' => 'instagram_carousel',
            'bg_class' => null,
            'status' => true,
            'sort_order' => 45,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $posts = [
            ['frontend_assets/images/our-story.jpg', 'Our story, one brushstroke at a time. #DESIGNDHAGA #HANDPAINT', 'IMAGE', 10],
            ['frontend_assets/images/hand-painted-portfolio.jpg', 'Hand-painted details that make every outfit unique. #DESIGNDHAGA', 'IMAGE', 20],
            ['frontend_assets/images/graphic-design-portfolio.jpg', 'Graphics that speak your identity. #DESIGNDHAGA #GRAPHICS', 'IMAGE', 30],
            ['frontend_assets/images/our-online-store.jpg', 'Explore our latest creations online. #DESIGNDHAGA', 'IMAGE', 40],
            ['frontend_assets/images/hand-painting-fabric-image.png', 'Your idea, our brush. #HANDPAINT #DESIGNDHAGA', 'VIDEO', 50],
            ['frontend_assets/images/graphics-image.png', 'Design that reflects who you are. #DESIGNDHAGA', 'IMAGE', 60],
        ];

        foreach ($posts as [$image, $caption, $mediaType, $sortOrder]) {
            DB::table('home_section_items')->insert([
                'home_section_id' => $sectionId,
                'title' => null,
                'subtitle' => $caption,
                'body' => null,
                'image' => $image,
                'link_text' => null,
                'link_url' => 'https://www.instagram.com/design.dhaga',
                'icon' => $mediaType,
                'status' => true,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $sectionId = DB::table('home_sections')->where('key', 'instagram_feed')->value('id');

        if ($sectionId) {
            DB::table('home_section_items')->where('home_section_id', $sectionId)->delete();
            DB::table('home_sections')->where('id', $sectionId)->delete();
        }
    }
};
