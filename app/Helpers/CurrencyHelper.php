<?php

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

function getIconsByCategory($category){
    $categoryIcons = [
        "1" => "Natural Fibre",
        "2" => "Hand Painted",
        "3" => "Made in India",
        "4" => "Limited Edition",
        "5" => "Timeless Appeal",
        "6" => "Pack of 1"
    ];
    return $categoryIcons;
}

function getCartItemsCount(){
    $cartItemsCount = 0;
    if (Auth::check() && Auth::user()->utype === 'USR') {
        $cartItemsCount = Cart::where('user_id', Auth::id())->count();
    }
    return $cartItemsCount;
}

if (!function_exists('getProductUrl')) {
    function getProductUrl($product)
    {
        if (!$product || !$product->category) {
            return '#';
        }

        $category = $product->category;
        $parent   = $category->parent; // key change

        $parentSlug = $parent ? $parent->slug : $category->slug;

        $subcategorySlug = $parent ? $category->slug : null;

        return route('shop.product', [
            'category' => $parentSlug,
            'subcategory' => $subcategorySlug,
            'product' => $product->slug
        ]);
    }
}