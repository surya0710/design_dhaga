<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('button_target')->default('_self');
            $table->string('layout')->nullable();
            $table->string('bg_class')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('home_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_section_id')->constrained('home_sections')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('link_text')->nullable();
            $table->string('link_url')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $sections = [
            ['key' => 'desktop_info', 'title' => null, 'subtitle' => null, 'body' => null, 'image' => null, 'button_text' => null, 'button_url' => null, 'layout' => 'three_columns', 'bg_class' => null, 'sort_order' => 10],
            ['key' => 'mobile_features', 'title' => null, 'subtitle' => null, 'body' => null, 'image' => null, 'button_text' => null, 'button_url' => null, 'layout' => 'three_icons', 'bg_class' => null, 'sort_order' => 20],
            ['key' => 'idea_brush', 'title' => 'Your Idea. Our Brush.', 'subtitle' => null, 'body' => "At Design Dhaga, we believe that fashion is a form of self-expression. That's why we create outfits that reflect your personality, values, and style. Whether you're looking for a statement piece or a wardrobe staple, our team of skilled designers and artisans will work with you to bring your vision to life.\n\nEvery outfit is hand-painted, one of a kind.\nYour story guides every brushstroke.\nNo repeats. No templates. Just personal art", 'image' => 'frontend_assets/images/hand-painting-fabric-image.png', 'button_text' => 'Customize Now', 'button_url' => '/contact-us#form', 'layout' => 'image_left', 'bg_class' => 'bg-body-secondary', 'sort_order' => 30],
            ['key' => 'graphics_design', 'title' => 'Design That Speaks Your Identity', 'subtitle' => null, 'body' => "At Design Dhaga, we see visual design as a powerful language one that expresses a brand's values, personality, and purpose. From logos and brand identities to digital creatives, every design is crafted from scratch through close collaboration, ensuring it reflects you, not passing trends. Guided by your ideas and refined with intention, our work is personal, original, and meaningful designed to communicate your identity with clarity and authenticity.\n\nYour ideas shape every detail\nCreated from scratch, with intention\nPersonal, original, and meaningful\nDesigned to reflect identity", 'image' => 'frontend_assets/images/graphics-image.png', 'button_text' => 'Customize Now', 'button_url' => '/contact-us#form', 'layout' => 'image_right', 'bg_class' => 'bg-body-primary', 'sort_order' => 40],
            ['key' => 'who_we_are', 'title' => 'Where Art, Fabric & Design Come Together', 'subtitle' => 'A closer look at who we are.', 'body' => null, 'image' => null, 'button_text' => null, 'button_url' => null, 'layout' => 'carousel_cards', 'bg_class' => null, 'sort_order' => 50],
            ['key' => 'inspired_art', 'title' => 'Inspired By Art, Powered By Design', 'subtitle' => 'Read the Story and Meet The Makers', 'body' => "Together, let's discover a better life\n#DESIGNDHAGA #HANDPAINT #GRAPHICS", 'image' => null, 'button_text' => null, 'button_url' => null, 'layout' => 'icons', 'bg_class' => 'bg-body-primary', 'sort_order' => 60],
        ];

        $sectionIds = [];

        foreach ($sections as $section) {
            $key = $section['key'];
            $section['status'] = true;
            $section['created_at'] = $now;
            $section['updated_at'] = $now;
            $sectionIds[$key] = DB::table('home_sections')->insertGetId($section);
        }

        $items = [
            ['desktop_info', 'Art Meets', 'Craftsmanship', null, null, null, null, null, 10],
            ['desktop_info', 'Exclusive Designs', 'Premium Detailing', null, null, null, null, null, 20],
            ['desktop_info', 'Fully Customizable', 'Your Idea, Our Artwork', null, null, null, null, null, 30],
            ['mobile_features', 'Easy Delivery', null, null, 'frontend_assets/images/easy-delivery-process.svg', null, null, null, 10],
            ['mobile_features', 'Exquisite Product', null, null, 'frontend_assets/images/exquisite-product.svg', null, null, null, 20],
            ['mobile_features', 'Intricate Design', null, null, 'frontend_assets/images/intricate-design.svg', null, null, null, 30],
            ['who_we_are', 'Our Story', null, null, 'frontend_assets/images/our-story.jpg', null, '/about-us', null, 10],
            ['who_we_are', 'Hand-Painted Portfolio', null, null, 'frontend_assets/images/hand-painted-portfolio.jpg', null, '/portfolio', null, 20],
            ['who_we_are', 'Our Online Store', null, null, 'frontend_assets/images/our-online-store.jpg', null, '#', null, 30],
            ['who_we_are', 'Graphic Design Portfolio', null, null, 'frontend_assets/images/graphic-design-portfolio.jpg', null, '/portfolio#graphics-gallery', null, 40],
            ['inspired_art', 'Timeless', null, null, 'frontend_assets/images/icons/TimeLess icon.svg', null, null, null, 10],
            ['inspired_art', 'Easy', null, null, 'frontend_assets/images/icons/Easy Icon.svg', null, null, null, 20],
            ['inspired_art', 'Honest', null, null, 'frontend_assets/images/icons/Honest icon.svg', null, null, null, 30],
        ];

        foreach ($items as [$sectionKey, $title, $subtitle, $body, $image, $linkText, $linkUrl, $icon, $sortOrder]) {
            if (empty($sectionIds[$sectionKey])) {
                continue;
            }

            DB::table('home_section_items')->insert([
                'home_section_id' => $sectionIds[$sectionKey],
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'image' => $image,
                'link_text' => $linkText,
                'link_url' => $linkUrl,
                'icon' => $icon,
                'status' => true,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('home_section_items');
        Schema::dropIfExists('home_sections');
    }
};
