<?php


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
    if(session()->has('cart')){
        $cartItemsCount = count(session()->get('cart'));
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