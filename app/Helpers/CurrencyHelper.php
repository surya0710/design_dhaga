<?php


function getIconsByCategory($category){
    $categoryIcons = [
        "Dupatta" => [
            "1" => "Natural Fibre",
            "2" => "Hand Painted",
            "3" => "Made in India",
            "4" => "Limited Edition",
            "5" => "Timeless Appeal",
            "6" => "Pack of 1"
        ]
    ];
    return $categoryIcons[$category];
}

function getCartItemsCount(){
    $cartItemsCount = 0;
    if(session()->has('cart')){
        $cartItemsCount = count(session()->get('cart'));
    }
    return $cartItemsCount;
}