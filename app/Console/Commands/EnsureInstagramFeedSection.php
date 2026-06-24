<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EnsureInstagramFeedSection extends Command
{
    protected $signature = 'home:ensure-instagram-feed';

    protected $description = 'Create the homepage Instagram feed section if it is missing';

    public function handle(): int
    {
        if (DB::table('home_sections')->where('key', 'instagram_feed')->exists()) {
            $this->info('Instagram feed section already exists.');

            return self::SUCCESS;
        }

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

        Cache::forget('home.sections');
        Cache::store('file')->forget('instagram.feed');
        Cache::forget('instagram.feed');

        $this->info('Instagram feed section created with ' . count($posts) . ' fallback posts.');

        return self::SUCCESS;
    }
}
