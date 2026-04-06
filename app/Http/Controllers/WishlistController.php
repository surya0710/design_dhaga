<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function add(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to add items to your wishlist.',
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $wishlist = Wishlist::firstOrCreate([
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
        ]);

        $created = $wishlist->wasRecentlyCreated;

        return response()->json([
            'status'       => true,
            'message'      => $created
                ? 'Product added to wishlist successfully.'
                : 'Product is already in your wishlist.',
            'in_wishlist'  => true,
            'product_id'   => $product->id,
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to manage your wishlist.',
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $deleted = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->delete();

        if (! $deleted) {
            return response()->json([
                'status' => false,
                'message' => 'Product was not found in your wishlist.',
                'in_wishlist' => false,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product removed from wishlist successfully.',
            'in_wishlist' => false,
            'product_id' => (int) $validated['product_id'],
        ]);
    }
}