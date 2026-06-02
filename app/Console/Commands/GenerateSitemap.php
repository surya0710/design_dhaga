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

            if (!in_array('GET', $methods)) {
                continue;
            }

            if (
                str_starts_with($uri, 'admin') ||
                str_contains($uri, '{') ||
                str_starts_with($uri, 'login') ||
                str_starts_with($uri, 'register') ||
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
                str_starts_with($uri, 'search') ||
                $uri === 'sanctum/csrf-cookie' ||
                $uri === 'up'
            )
            {
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
        | Blog Pages
        |--------------------------------------------------------------------------
        */
        Blog::where('status', 1)
            ->chunk(500, function ($blogs) use ($sitemap) {

                foreach ($blogs as $blog) {

                    $sitemap->add(
                        Url::create(route('blog.show', [
                            'slug' => $blog->slug
                        ]))
                            ->setPriority(0.7)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setLastModificationDate($blog->updated_at ?? now())
                    );
                }
            });

        /*
        |--------------------------------------------------------------------------
        | Shop Main Page
        |--------------------------------------------------------------------------
        */
        $sitemap->add(
            Url::create(route('shop.all'))
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setLastModificationDate(now())
        );

        /*
        |--------------------------------------------------------------------------
        | Category Pages
        |--------------------------------------------------------------------------
        */
        Category::where('status', 1)
            ->chunk(500, function ($categories) use ($sitemap) {

                foreach ($categories as $category) {

                    $sitemap->add(
                        Url::create(
                            route('shop.index', [
                                'category' => $category->slug
                            ])
                        )
                            ->setPriority(0.8)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setLastModificationDate($category->updated_at ?? now())
                    );
                }
            });

        /*
        |--------------------------------------------------------------------------
        | Product Pages
        |--------------------------------------------------------------------------
        */
        Product::where('status', 1)
            ->with('category')
            ->chunk(500, function ($products) use ($sitemap) {

                foreach ($products as $product) {

                    if (!$product->category) {
                        continue;
                    }

                    $category = $product->category;

                    // Child category
                    if (!empty($category->parent_id)) {

                        $parentCategory = Category::find($category->parent_id);

                        if (!$parentCategory) {
                            continue;
                        }

                        $productUrl = route('shop.product', [
                            'category'    => $parentCategory->slug,
                            'subcategory' => $category->slug,
                            'product'     => $product->slug,
                        ]);

                    } else {

                        // Fallback for products directly attached to parent category
                        $productUrl = route('shop.product', [
                            'category'    => $category->slug,
                            'subcategory' => $category->slug,
                            'product'     => $product->slug,
                        ]);
                    }

                    $sitemap->add(
                        Url::create($productUrl)
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