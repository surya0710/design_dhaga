<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Pages;
use App\Models\Product;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'generate:sitemap';

    protected $description = 'Generate sitemap.xml';

    /** @var array<string, bool> */
    private array $addedUrls = [];

    public function handle()
    {
        $sitemap = Sitemap::create();

        $this->addStaticPages($sitemap);
        $this->addCmsPages($sitemap);
        $this->addBlogs($sitemap);
        $this->addCategories($sitemap);
        $this->addProducts($sitemap);

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully with ' . count($this->addedUrls) . ' URLs.');
    }

    private function addStaticPages(Sitemap $sitemap): void
    {
        $routes = [
            'home' => ['priority' => 1.0, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'about-us' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'contact-us' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'terms-and-condition' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'return-policy' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'shipping-policy' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'privacy-policy' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'store' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'blogs' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'collaborations' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'portfolio' => ['priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            'shop.all' => ['priority' => 0.9, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
        ];

        foreach ($routes as $routeName => $options) {
            $this->addSitemapUrl(
                $sitemap,
                route($routeName),
                $options['priority'],
                $options['frequency']
            );
        }
    }

    private function addCmsPages(Sitemap $sitemap): void
    {
        Pages::where('status', 1)
            ->orderBy('id')
            ->chunk(200, function ($pages) use ($sitemap) {
                foreach ($pages as $page) {
                    $slug = trim((string) $page->slug, '/');

                    if ($slug === '' || $slug === '/') {
                        continue;
                    }

                    $this->addSitemapUrl(
                        $sitemap,
                        url($slug),
                        0.8,
                        Url::CHANGE_FREQUENCY_WEEKLY,
                        $page->updated_at
                    );
                }
            });
    }

    private function addBlogs(Sitemap $sitemap): void
    {
        Blog::where('status', 1)
            ->orderBy('id')
            ->chunk(500, function ($blogs) use ($sitemap) {
                foreach ($blogs as $blog) {
                    $this->addSitemapUrl(
                        $sitemap,
                        route('blog.show', ['slug' => $blog->slug]),
                        0.7,
                        Url::CHANGE_FREQUENCY_WEEKLY,
                        $blog->updated_at
                    );
                }
            });
    }

    private function addCategories(Sitemap $sitemap): void
    {
        Category::where('status', 1)
            ->with('parent')
            ->orderBy('id')
            ->chunk(500, function ($categories) use ($sitemap) {
                foreach ($categories as $category) {
                    $url = getCategoryUrl($category);

                    if ($url === '#') {
                        continue;
                    }

                    $this->addSitemapUrl(
                        $sitemap,
                        $url,
                        0.8,
                        Url::CHANGE_FREQUENCY_WEEKLY,
                        $category->updated_at
                    );
                }
            });
    }

    private function addProducts(Sitemap $sitemap): void
    {
        Product::where('status', 1)
            ->with(['category.parent'])
            ->orderBy('id')
            ->chunk(500, function ($products) use ($sitemap) {
                foreach ($products as $product) {
                    $url = getProductUrl($product);

                    if ($url === '#') {
                        continue;
                    }

                    $this->addSitemapUrl(
                        $sitemap,
                        $url,
                        0.9,
                        Url::CHANGE_FREQUENCY_DAILY,
                        $product->updated_at
                    );
                }
            });
    }

    private function addSitemapUrl(
        Sitemap $sitemap,
        string $url,
        float $priority,
        string $changeFrequency,
        $lastModified = null
    ): void {
        $normalizedUrl = rtrim($url, '/');

        if ($normalizedUrl === '') {
            $normalizedUrl = url('/');
        }

        if (isset($this->addedUrls[$normalizedUrl])) {
            return;
        }

        $this->addedUrls[$normalizedUrl] = true;

        $sitemap->add(
            Url::create($normalizedUrl)
                ->setPriority($priority)
                ->setChangeFrequency($changeFrequency)
                ->setLastModificationDate($lastModified ?? now())
        );
    }
}
