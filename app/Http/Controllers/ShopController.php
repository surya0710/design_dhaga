<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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

    public function product_details(Request $request, $category = null, $subcategory = null, $slug = null)
    {
        $categories = $this->categories;
        
        $product = Product::where('slug', $slug)
            ->with([
                'galleryImages',
                'artisanImages',
                'productAttributes',
                'category'
            ])
            ->firstOrFail();

        $reviews = $product->reviews()->where('approved', 1)->get();

        // Build unified image array: main + gallery
        $galleryPaths = $product->galleryImages->pluck('image')->toArray();

        return view('frontend.product', compact('product', 'categories', 'galleryPaths', 'reviews'));
    }
}