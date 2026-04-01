<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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
        $cart = session()->get('cart', []);

        $cartItems = collect($cart);

        $subtotal = $cartItems->sum(function ($item) {
            return $item['price'] * $item['quantity'];
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

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $price,
                'image'    => $product->image,
                'quantity' => $request->quantity,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success'    => true,
            'message'    => 'Added to cart',
            'cart_count' => count($cart),
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

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

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

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Cart updated');
    }
}