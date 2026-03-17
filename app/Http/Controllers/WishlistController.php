<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Product;

class WishlistController extends Controller
{
    public function add_to_wishlist(Request $request)
    {
        $product = Product::findOrFail($request->id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        if ($product->status != '1') {
            return response()->json(['error' => 'Product is not available'], 400);
        }

        // Check if the product is already in the wishlist
        $wishlist = Cart::instance('wishlist')->content();
        foreach ($wishlist as $item) {
            if ($item->id == $product->id) {
                return response()->json(['error' => 'Product already in wishlist'], 400);
            }
        }
        if ($product->sale_price > 0) {
            $price = $product->sale_price;
        } else {
            $price = $product->regular_price;
        }
        $allimages = $product->images ? explode(',', $product->images) : [];
        if (count($allimages) > 0) {
            $firstimage = $allimages[0];
        } else {
            $firstimage = 'default.png'; // Fallback image
        }

        Cart::instance('wishlist')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $request->qty ?: 1,
            'price' => $product->sale_price ?: $product->regular_price,
            'options' => [
                'slug' => $product->slug
            ]
        ])->associate(Product::class);

        return response()->json([
            'success' => true,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'product_image' => asset('uploads/products/thumbnails/' . $firstimage),
            'product_price' => format_currency($price, session('currency', 'INR')),
            'product_qty' => $request->qty,
            'cart_count' => Cart::instance('wishlist')->count()
        ]);
    }

    public function index()
    {
        $items = Cart::instance('wishlist')->content();
         //related products
        $related_products = Product::where('status', '1')->orderBy('id', 'desc')->take(4)->get();
        $related_products->each(function ($related_product) {
            if ($related_product->sale_price > 0) {
                $related_product->price = $related_product->sale_price;
            } else {
                $related_product->price = $related_product->regular_price;
            }
        });
        return view('wishlist', compact('items', 'related_products'));
    }
    public function remove($rowId)
    {
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back()->with('success', 'Product removed from wishlist');
    }
    public function update(Request $request, $rowId)
    {
        $qty = $request->input('qty');
        Cart::instance('wishlist')->update($rowId, $qty);
        return redirect()->back()->with('success', 'Product updated successfully');
    }
    // public function clear()
    // {
    //     Cart::instance('wishlist')->destroy();
    //     return redirect()->back()->with('success', 'Wishlist cleared successfully');
    // }
    // public function wishlistModal()
    // {
    //     $items = Cart::instance('wishlist')->content();
    //     return view('partials.wishlist_modal', compact('items'));
    // }

    // public function getSessionItems()
    // {
    //     $wishlistItems = Cart::instance('wishlist')->content();

    //     $items = $wishlistItems->map(function ($item) {
    //         return [
    //             'id' => $item->id,
    //             'product_name' => $item->name,
    //             'product_slug' => $item->options->slug,
    //             'product_image' => $item->options->image,
    //             'product_price' => $item->price,
    //             'product_qty' => $item->qty,
    //         ];
    //     });

    //     return response()->json([
    //         'items' => $items,
    //         'count' => $wishlistItems->count(),
    //     ]);
    // }

}
