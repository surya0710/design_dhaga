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
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
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

        // SHOW ALL PRODUCTS
        if (!$categorySlug) {

            $category = null;

            $products = Product::where('status', 1)
                ->with('activeVariants:id,product_id,price')
                ->orderBy('id', 'desc')
                ->get();

        }

        // SUBCATEGORY PRODUCTS
        elseif ($subcategorySlug) {

            $category = Category::where('slug', $subcategorySlug)
                ->firstOrFail();

            $products = Product::where('status', 1)
                ->with('activeVariants:id,product_id,price')
                ->where('category_id', $category->id)
                ->orderBy('id', 'desc')
                ->get();

        }

        // CATEGORY + CHILD CATEGORY PRODUCTS
        else {

            $category = Category::where('slug', $categorySlug)
                ->with('children')
                ->firstOrFail();

            $subcategoryIds = $category->children
                ->pluck('id')
                ->toArray();

            $products = Product::where('status', 1)
                ->with('activeVariants:id,product_id,price')
                ->where(function ($q) use ($category, $subcategoryIds) {

                    $q->where('category_id', $category->id)
                    ->orWhereIn('category_id', $subcategoryIds);

                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $faqs = Faqs::where([
            'status'    => 1,
            'page_slug' => 'shop'
        ])->get();

        $highlights = $this->highlights;

        return view('frontend.shop', compact(
            'products',
            'category',
            'categories',
            'menu',
            'highlights',
            'faqs'
        ));
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
        return view('frontend.shop', compact('products', 'categories', 'category', 'menu', 'highlights'));
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
            'highlights' => $highlights
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

        $products = Product::where('status', 1)
            ->with('activeVariants:id,product_id,price')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'LIKE', '%' . $query . '%')
                        ->orWhere('slug', 'LIKE', '%' . $query . '%')
                        ->orWhere('description', 'LIKE', '%' . $query . '%')
                        ->orWhere('short_description', 'LIKE', '%' . $query . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.shop', compact(
            'products',
            'categories',
            'category',
            'menu',
            'highlights'
        ));
    }
}
