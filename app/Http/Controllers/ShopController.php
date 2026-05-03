<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Visitor;
use App\Models\Review;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected $categories;

    public function __construct()
    {
        $this->categories = Category::where('status', 1)
            ->where(function ($query) {
                $query->whereNull('parent_id')
                    ->orWhere('parent_id', 0);
            })
            ->with('children')
            ->get();
    }


    public function category_products(Request $request, $categorySlug = null, $subcategorySlug = null)
    {
        $categories = $this->categories;

        if ($subcategorySlug) {
            $category = Category::where('slug', $subcategorySlug)->firstOrFail();

            $products = Product::where('status', 1)->where('category_id', $category->id)->orderBy('id', 'desc')->get();
        } else {
            $category = Category::where('slug', $categorySlug)->with('children')->firstOrFail();

            $subcategoryIds = $category->children->pluck('id')->toArray();

            $products = Product::where('status', 1)
                ->where(function ($q) use ($category, $subcategoryIds) {
                    $q->where('category_id', $category->id)
                    ->orWhereIn('category_id', $subcategoryIds);
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('frontend.shop', compact('products', 'category', 'categories'));
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
        $products = Product::whereIn('id', $wishlistProductIds)->get();
        return view('frontend.shop', compact('products', 'categories', 'category'));
    }

    public function product_details(Request $request, $category = null, $subcategory = null, $slug = null)
    {
        $categories = $this->categories;

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
        $relatedProducts = Product::select('id','name','slug','image','category_id')
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
            'country' => $country
        ]);
    }
}