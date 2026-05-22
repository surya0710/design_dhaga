<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;

class GenerateSitemap extends Command
{
    protected $signature = 'generate:sitemap';
    protected $description = 'Generate sitemap.xml';

    public function handle()
    {
        $sitemap = Sitemap::create();

        /*
        |--------------------------------------------------------------------------
        | Static Routes
        |--------------------------------------------------------------------------
        */
        foreach (Route::getRoutes() as $route) {

            $uri = $route->uri();
            $methods = $route->methods();

            // Only GET routes
            if (!in_array('GET', $methods)) {
                continue;
            }

            // Skip dynamic/admin/auth/system routes
            if (
                str_starts_with($uri, 'admin') ||
                str_contains($uri, '{') ||
                str_starts_with($uri, 'login') ||
                str_starts_with($uri, 'logout') ||
                str_starts_with($uri, 'account') ||
                str_starts_with($uri, 'cart') ||
                str_starts_with($uri, 'checkout') ||
                str_starts_with($uri, 'order') ||
                str_starts_with($uri, 'wishlist') ||
                str_starts_with($uri, 'auth') ||
                str_starts_with($uri, 'email') ||
                str_starts_with($uri, 'password') ||
                str_starts_with($uri, 'forgot-password') ||
                str_starts_with($uri, 'reset-password') ||
                $uri === 'sanctum/csrf-cookie' ||
                $uri === 'up'
            ) {
                continue;
            }

            $sitemap->add(
                Url::create(url($uri === '/' ? '/' : '/' . $uri))
                    ->setPriority($uri === '/' ? 1.0 : 0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setLastModificationDate(now())
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Blogs
        |--------------------------------------------------------------------------
        */
        Blog::where('status', 1)
            ->chunk(500, function ($blogs) use ($sitemap) {

                foreach ($blogs as $blog) {

                    $sitemap->add(
                        Url::create(url('/blogs/' . $blog->slug))
                            ->setPriority(0.7)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setLastModificationDate($blog->updated_at ?? now())
                    );
                }
            });

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        Category::where('status', 1)
            ->chunk(500, function ($categories) use ($sitemap) {

                foreach ($categories as $category) {

                    $sitemap->add(
                        Url::create(url('/category/' . $category->slug))
                            ->setPriority(0.8)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setLastModificationDate($category->updated_at ?? now())
                    );
                }
            });

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */
        Product::where('status', 1)
            ->chunk(500, function ($products) use ($sitemap) {

                foreach ($products as $product) {

                    $sitemap->add(
                        Url::create(url('/product/' . $product->slug))
                            ->setPriority(0.9)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                            ->setLastModificationDate($product->updated_at ?? now())
                    );
                }
            });

        /*
        |--------------------------------------------------------------------------
        | Write Sitemap
        |--------------------------------------------------------------------------
        */
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');
    }
}