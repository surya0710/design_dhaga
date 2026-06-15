<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Visitor;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\HomepageHighlight;
use App\Models\Faqs;
use App\Models\Pages;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    private const PRODUCTS_PER_PAGE = 20;

    protected $categories;
    protected $menu;
    protected $highlights;

    public function __construct()
    {
        $this->categories = Cache::remember('site.active_parent_categories', 60, function () {
            return Category::where('status', 1)
                ->where(function ($query) {
                    $query->whereNull('parent_id')
                        ->orWhere('parent_id', 0);
                })
                ->with('children')
                ->get();
        });

        $this->menu = Cache::remember('site.active_menus', 60, function () {
            return Menu::where('is_active', 1)->orderBy('created_at', 'asc')->get();
        });

        $this->highlights = Cache::remember('site.homepage_highlights', 60, function () {
            return HomepageHighlight::where('status', 1)->get();
        });
    }


    public function category_products(Request $request, $categorySlug = null, $subcategorySlug = null)
    {
        $categories = $this->categories;
        $menu       = $this->menu;

        if (!$categorySlug) {
            $category = null;
        } elseif ($subcategorySlug) {
            $category = Category::where('slug', $subcategorySlug)->with('parent')->firstOrFail();
        } else {
            $category = Category::where('slug', $categorySlug)
                ->with('children')
                ->firstOrFail();
        }

        $sort = $request->get('sort', 'newest');
        $query = $this->buildProductsQuery($categorySlug, $subcategorySlug);
        $totalProducts = (clone $query)->count('products.id');

        $products = $this->applyProductSort($query, $sort)
            ->limit(self::PRODUCTS_PER_PAGE)
            ->get();

        $priceBounds = $this->getPriceBounds();
        $hasMoreProducts = $totalProducts > self::PRODUCTS_PER_PAGE;
        $showFilters = true;

        $faqs = Faqs::where([
            'status'    => 1,
            'page_slug' => 'shop'
        ])->get();

        $highlights = $this->highlights;

        $slug           = $category ?? 'shop';
        $pageContent    = Pages::where('slug', $slug)->first() ?? [];

        return view('frontend.shop', compact(
            'products',
            'category',
            'categories',
            'menu',
            'highlights',
            'faqs',
            'priceBounds',
            'hasMoreProducts',
            'totalProducts',
            'showFilters',
            'pageContent'
        ));
    }

    public function loadProducts(Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));
        $categorySlug = $request->get('url_category');
        $subcategorySlug = $request->get('url_subcategory');
        $search = trim((string) $request->get('q', ''));

        $minPrice = $request->filled('min_price') ? (float) $request->get('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->get('max_price') : null;
        $sort = $request->get('sort', 'newest');

        $query = $this->buildProductsQuery($categorySlug, $subcategorySlug, $minPrice, $maxPrice, $search);
        $total = (clone $query)->count('products.id');

        $products = $this->applyProductSort($query, $sort)
            ->skip(($page - 1) * self::PRODUCTS_PER_PAGE)
            ->take(self::PRODUCTS_PER_PAGE)
            ->get();

        $html = view('frontend.partials.shop-products-grid', ['products' => $products])->render();

        return response()->json([
            'html' => $html,
            'has_more' => ($page * self::PRODUCTS_PER_PAGE) < $total,
            'total' => $total,
            'page' => $page,
        ]);
    }

    private function buildProductsQuery(
        ?string $categorySlug = null,
        ?string $subcategorySlug = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $search = null
    ) {
        $query = Product::query()
            ->where('products.status', 1)
            ->select('products.*')
            ->with([
                'activeVariants:id,product_id,price',
                'category:id,name,slug,parent_id',
                'category.parent:id,name,slug',
            ]);

        if ($subcategorySlug) {
            $category = Category::where('slug', $subcategorySlug)->firstOrFail();
            $query->where('products.category_id', $category->id);
        } elseif ($categorySlug) {
            $category = Category::where('slug', $categorySlug)
                ->with('children')
                ->firstOrFail();

            $subcategoryIds = $category->children->pluck('id')->toArray();

            $query->where(function ($q) use ($category, $subcategoryIds) {
                $q->where('products.category_id', $category->id)
                    ->orWhereIn('products.category_id', $subcategoryIds);
            });
        }

        if ($minPrice !== null && $maxPrice !== null) {
            $query->where(function ($q) use ($minPrice, $maxPrice) {
                $q->where(function ($sub) use ($minPrice, $maxPrice) {
                    $sub->whereHas('activeVariants')
                        ->whereRaw(
                            '(SELECT MIN(price) FROM product_variants WHERE product_id = products.id AND is_active = 1 AND quantity > 0) BETWEEN ? AND ?',
                            [$minPrice, $maxPrice]
                        );
                })->orWhere(function ($sub) use ($minPrice, $maxPrice) {
                    $sub->whereDoesntHave('activeVariants', function ($vq) {
                        $vq->where('is_active', true)->where('quantity', '>', 0);
                    })->whereRaw(
                        'COALESCE(NULLIF(products.sale_price, 0), products.regular_price) BETWEEN ? AND ?',
                        [$minPrice, $maxPrice]
                    );
                });
            });
        }

        $search = trim((string) $search);

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('products.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('products.slug', 'LIKE', '%' . $search . '%')
                    ->orWhere('products.description', 'LIKE', '%' . $search . '%')
                    ->orWhere('products.short_description', 'LIKE', '%' . $search . '%');
            });
        }

        return $query;
    }

    private function applyProductSort($query, ?string $sort = 'newest')
    {
        return match ($sort) {
            'price_low' => $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.regular_price) ASC'),
            'price_high' => $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.regular_price) DESC'),
            'name_asc' => $query->orderBy('products.name', 'asc'),
            'name_desc' => $query->orderBy('products.name', 'desc'),
            default => $query->orderBy('products.id', 'desc'),
        };
    }

    private function getPriceBounds(): array
    {
        return Cache::remember('shop.price_bounds', 300, function () {
            $variantMin = DB::table('product_variants')
                ->where('is_active', 1)
                ->where('quantity', '>', 0)
                ->min('price');

            $variantMax = DB::table('product_variants')
                ->where('is_active', 1)
                ->where('quantity', '>', 0)
                ->max('price');

            $productMin = DB::table('products')
                ->where('status', 1)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->where('is_active', 1)
                        ->where('quantity', '>', 0);
                })
                ->min(DB::raw('COALESCE(NULLIF(sale_price, 0), regular_price)'));

            $productMax = DB::table('products')
                ->where('status', 1)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->where('is_active', 1)
                        ->where('quantity', '>', 0);
                })
                ->max(DB::raw('COALESCE(NULLIF(sale_price, 0), regular_price)'));

            $min = (int) floor(min(
                $variantMin ?? PHP_INT_MAX,
                $productMin ?? PHP_INT_MAX
            ));
            $max = (int) ceil(max(
                $variantMax ?? 0,
                $productMax ?? 0
            ));

            if ($min === PHP_INT_MAX) {
                $min = 0;
            }

            if ($max <= $min) {
                $max = $min + 10000;
            }

            return ['min' => $min, 'max' => $max];
        });
    }


    public function wishlist()
    {
        $category = (object) [
            'meta_title' => 'Your Wishlist',
            'name' => 'Wishlist',
            'slug' => 'wishlist',
        ];
        $categories = $this->categories;
        $wishlistProductIds = Wishlist::where('user_id', auth()->user()->id)->pluck('product_id')->toArray();
        $products           = Product::whereIn('id', $wishlistProductIds)
            ->with('activeVariants:id,product_id,price')
            ->get();
        $menu               = $this->menu;
        $highlights         = $this->highlights;
        $showFilters = false;
        $pageContent = Pages::where('slug', 'wishlist')->first()
            ?? (object) [
                'meta_title' => 'Your Wishlist',
                'meta_description' => 'Your saved Design Dhaga products.',
                'meta_keywords' => 'wishlist, saved products',
                'meta_image' => 'og-home.jpg',
                'heading' => null,
                'content' => null,
                'canonical_url' => url()->current(),
            ];

        return view('frontend.shop', compact('products', 'categories', 'category', 'menu', 'highlights', 'showFilters', 'pageContent'));
    }

    public function product_details(Request $request, $category = null, $subcategory = null, $slug = null)
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $highlights     = $this->highlights;

        $visitorId = $request->cookie('visitor_id');

        $visitor = null;

        if ($visitorId) {
            $visitor = Visitor::where('visitor_id', $visitorId)->first();
        }

        $country = $visitor->country ?? 'India';

        // ✅ Product + limited reviews (fast load)
        $product = Product::where('slug', $slug)
            ->with([
                'galleryImages:id,product_id,image',
                'artisanImages:id,product_id,image,title,description',
                'productAttributes:id,product_id,key,value',
                'variants:id,product_id,size,fabric_type,sku,price,quantity,is_active',
                'activeVariants:id,product_id,size,fabric_type,sku,price,quantity,is_active',
                'category:id,name,slug,parent_id',
                'icons:id,product_id,image,text',

                // 🔥 Only for preview (10 latest)
                'reviews' => function ($q) {
                    $q->latest()
                    ->select('id','product_id','user_id','rating','review','image','created_at')
                    ->with('user:id,name')
                    ->limit(10);
                }
            ])
            ->firstOrFail();

        // ✅ Review stats (correct calculation)
        $reviewStats = Review::where('product_id', $product->id)
            ->selectRaw('COUNT(*) as total, AVG(rating) as avg')
            ->first();

        // ✅ All reviews (for popup)
        $allReviews = Review::where('product_id', $product->id)
            ->with('user:id,name')
            ->latest()
            ->get();

        // ✅ Related products
        $relatedProducts = Product::select('id','name','slug','image','category_id', 'sale_price','regular_price')
            ->with('activeVariants:id,product_id,price')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category.parent'])
            ->where('status', 1)
            ->latest()
            ->limit(8)
            ->get();

        // ✅ Wishlist check
        $isInWishlist = auth()->check()
            ? Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists()
            : false;

        $faqs = Faqs::where([
            'status'    => 1,
            'page_slug' => 'shop'
        ])->get();

        return view('frontend.product', [
            'product' => $product,
            'categories' => $categories,
            'galleryPaths' => $product->galleryImages->pluck('image')->toArray(),
            'relatedProducts' => $relatedProducts,
            'totalReviews' => $reviewStats->total ?? 0,
            'averageRating' => round($reviewStats->avg ?? 0, 1),
            'allReviews' => $allReviews,
            'isInWishlist' => $isInWishlist,
            'country' => $country,
            'menu' => $menu,
            'highlights' => $highlights,
            'faqs' => $faqs
        ]);
    }

    public function search(Request $request)
    {
        $categories = $this->categories;
        $menu = $this->menu;
        $highlights = $this->highlights;

        $query = trim($request->get('q'));

        $category = (object) [
            'name' => $query ? 'Search results for: ' . $query : 'Search Products',
            'slug' => 'search',
            'meta_title' => 'Search Products',
        ];

        $pageContent = Pages::where('slug', 'search')->first()
            ?? Pages::where('slug', 'shop')->first()
            ?? (object) [
                'meta_title' => 'Search Products',
                'meta_description' => 'Search products on Design Dhaga.',
                'meta_keywords' => 'search products, design dhaga',
                'meta_image' => 'og-home.jpg',
                'heading' => null,
                'content' => null,
                'canonical_url' => url()->current(),
            ];

        $sort = $request->get('sort', 'newest');
        $productsQuery = $this->buildProductsQuery(search: $query);
        $totalProducts = (clone $productsQuery)->count('products.id');

        $products = $this->applyProductSort($productsQuery, $sort)
            ->limit(self::PRODUCTS_PER_PAGE)
            ->get();

        $priceBounds = $this->getPriceBounds();
        $hasMoreProducts = $totalProducts > self::PRODUCTS_PER_PAGE;
        $showFilters = true;
        $faqs = collect();

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products,
            $totalProducts,
            self::PRODUCTS_PER_PAGE,
            1,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $products->withPath($request->url())
            ->withQueryString();

        return view('frontend.shop', compact(
            'products',
            'categories',
            'category',
            'menu',
            'highlights',
            'showFilters',
            'pageContent',
            'priceBounds',
            'hasMoreProducts',
            'totalProducts',
            'faqs'
        ));
    }

    public function searchSuggestions(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('status', 1)
            ->select('id', 'name', 'slug', 'image', 'category_id', 'regular_price', 'sale_price')
            ->with(['category.parent', 'activeVariants:id,product_id,price'])
            ->where(function ($productQuery) use ($query) {
                $productQuery->where('name', 'LIKE', '%' . $query . '%')
                    ->orWhere('slug', 'LIKE', '%' . $query . '%')
                    ->orWhere('short_description', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(function (Product $product) {
                return [
                    'name' => $product->name,
                    'url' => getProductUrl($product),
                    'image' => Storage::url($product->image),
                    'price' => '₹' . number_format($product->display_price, 0),
                ];
            });

        return response()->json($products);
    }
}
