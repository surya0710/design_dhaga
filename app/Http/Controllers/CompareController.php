<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index()
    {
        // $compare = session()->get('compare', []);
        // // dd($compare);
        // $products = Product::whereIn('id', $compare)->get();

        return view('layouts.compare-modal', compact('products'));
    }
    public function main()
    {
        $compare = session()->get('compare', []);
        // dd($compare);
        $products = Product::whereIn('id', $compare)->get();
        if ($products->count() < 2) {
            return redirect()->back()->with('error', 'You need at least 2 products to compare.');
        }
        if ($products->count() > 3) {
            $products = $products->take(3); // Limit to 4 products
        }
        $products->each(function ($product) {
            if ($product->sale_price > 0) {
                $product->price = format_currency($product->sale_price, session('currency', 'INR'));
            } else {
                $product->price = format_currency($product->regular_price, session('currency', 'INR'));
            }
        });

        return view('compare-products', compact('products'));
    }

    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $compare = session()->get('compare', []);

        if (!in_array($productId, $compare)) {
            $compare[] = $productId;
            $compare = array_slice($compare, -3);  // keep last 3 only
            session(['compare' => $compare]);
        }
        // Set flash flag to open modal after reload
        session()->flash('openModal', true);

        // return response()->json(['success' => true, 'compare_count' => count($compare)]);
        return redirect()->back()->with('success', 'Product added to compare list.')->with('openModal', true);
    }

    public function remove(Request $request)
    {
        $productId = $request->id;
        $compare = session()->get('compare', []);

        $compare = array_filter($compare, fn($id) => $id != $productId);
        session(['compare' => $compare]);

        // return response()->json(['success' => true, 'compare_count' => count($compare)]);
        return redirect()->back()->with('success', 'Product removed from compare list.');
    }

    public function clear()
    {
        session()->forget('compare');
        // return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Compare list cleared successfully.');
    }
}
