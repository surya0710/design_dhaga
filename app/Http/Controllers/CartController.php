<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $categories;

    public function __construct()
    {
        $this->categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with('children')
            ->get();
    }

    /**
     * Show Cart Page
     */
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $total = $subtotal;

        return view('frontend.cart', [
            'cartItems' => $cartItems,
            'subtotal'  => $subtotal,
            'total'     => $total,
            'categories'=> $this->categories
        ]);
    }

    /**
     * Add to Cart
     */
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first.',
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($request->product_id);

        $price = $product->sale_price ?? $product->regular_price;

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id'   => Auth::id(),
                'product_id'=> $product->id,
                'quantity'  => $request->quantity,
                'price'     => $price,
            ]);
        }

        $cartCount = Cart::where('user_id', Auth::id())->count();

        return response()->json([
            'success'    => true,
            'message'    => 'Added to cart',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Remove Item
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->delete();

        return redirect()->back()->with('success', 'Item removed');
    }

    /**
     * Update Quantity
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->update([
                'quantity' => $request->quantity
            ]);

        return redirect()->back()->with('success', 'Cart updated');
    }
}