<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Pages;
use App\Models\PortfolioCategory;
use App\Models\Product;
use Illuminate\Support\Collection;

class SitemapUrlService
{
    /**
     * CMS "pages" slugs that map to real public routes.
     * Other rows in `pages` are category SEO content (e.g. "Blouse", "Women")
     * and are not standalone URLs — shop category URLs come from addShopCategoryUrls().
     *
     * @var list<string>
     */
    private const PUBLIC_CMS_SLUGS = [
        'about-us',
        'contact-us',
        'terms-and-condition',
        'return-policy',
        'order-shipping-policy',
        'privacy-policy',
        'collaborations',
        'blogs',
        'portfolio',
        'shop'
    ];

    /** @var array<string, array<string, mixed>> */
    private array $urls = [];

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collect(): Collection
    {
        $this->urls = [];

        // CMS first so real pages keep an admin edit link; static fills any gaps.
        $this->addCmsPages();
        $this->addStaticPages();
        $this->addBlogs();
        $this->addShopCategoryUrls();
        $this->addPortfolioCategoryUrls();
        $this->addProducts();

        return collect(array_values($this->urls))
            ->sortBy([
                ['type', 'asc'],
                ['title', 'asc'],
            ])
            ->values();
    }

    public function count(): int
    {
        return $this->collect()->count();
    }

    /**
     * @return array<string, int>
     */
    public function countsByType(): array
    {
        return $this->collect()
            ->groupBy('type')
            ->map(fn (Collection $items) => $items->count())
            ->all();
    }

    private function addStaticPages(): void
    {
        $routes = [
            'home' => 'Home',
            'about-us' => 'About Us',
            'contact-us' => 'Contact Us',
            'terms-and-condition' => 'Terms & Conditions',
            'return-policy' => 'Return Policy',
            'shipping-policy' => 'Order & Shipping Policy',
            'privacy-policy' => 'Privacy Policy',
            'blogs' => 'Blogs',
            'collaborations' => 'Collaborations',
            'portfolio' => 'Portfolio',
            'shop.all' => 'Shop',
        ];

        foreach ($routes as $routeName => $title) {
            $this->addUrl(
                route($routeName),
                'Static Page',
                $title,
                'Active',
                null
            );
        }
    }

    private function addCmsPages(): void
    {
        Pages::where('status', 1)
            ->whereIn('slug', self::PUBLIC_CMS_SLUGS)
            ->orderBy('title')
            ->each(function (Pages $page) {
                $slug = trim((string) $page->slug, '/');

                if ($slug === '' || $slug === '/') {
                    return;
                }

                $this->addUrl(
                    url($slug),
                    'Page',
                    $page->title ?: $page->heading ?: $slug,
                    'Active',
                    route('admin.pages.edit', $page->id)
                );
            });
    }

    private function addBlogs(): void
    {
        Blog::where('status', 1)
            ->orderBy('title')
            ->each(function (Blog $blog) {
                $this->addUrl(
                    route('blog.show', ['slug' => $blog->slug]),
                    'Blog',
                    $blog->title,
                    'Active',
                    route('admin.blog.edit', $blog->id)
                );
            });
    }

    /**
     * Shop category listing URLs: /shop/{category} and /shop/{category}/{subcategory}.
     */
    private function addShopCategoryUrls(): void
    {
        Category::where('status', 1)
            ->with('parent')
            ->orderBy('name')
            ->each(function (Category $category) {
                $url = getCategoryUrl($category);

                if ($url === '#') {
                    return;
                }

                $this->addUrl(
                    $url,
                    'Category',
                    $category->name,
                    'Active',
                    route('admin.category.edit', $category->id)
                );
            });
    }

    private function addPortfolioCategoryUrls(): void
    {
        PortfolioCategory::where('status', 1)
            ->orderBy('name')
            ->each(function (PortfolioCategory $category) {
                $url = getPortfolioCategoryUrl($category);

                if ($url === '#') {
                    return;
                }

                $this->addUrl(
                    $url,
                    'Portfolio Category',
                    $category->name,
                    'Active',
                    route('admin.portfolio.categories.index')
                );
            });
    }

    private function addProducts(): void
    {
        Product::where('status', 1)
            ->with(['category.parent'])
            ->orderBy('name')
            ->each(function (Product $product) {
                $url = getProductUrl($product);

                if ($url === '#') {
                    return;
                }

                $this->addUrl(
                    $url,
                    'Product',
                    $product->name,
                    'Active',
                    route('admin.product.edit', $product->id)
                );
            });
    }

    private function addUrl(
        string $url,
        string $type,
        string $title,
        string $status,
        ?string $adminUrl
    ): void {
        $normalizedUrl = rtrim($url, '/');

        if ($normalizedUrl === '') {
            $normalizedUrl = rtrim(url('/'), '/');
        }

        if (isset($this->urls[$normalizedUrl])) {
            return;
        }

        $this->urls[$normalizedUrl] = [
            'type' => $type,
            'title' => $title,
            'url' => $normalizedUrl,
            'status' => $status,
            'admin_url' => $adminUrl,
        ];
    }
}
