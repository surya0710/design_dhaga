<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CouponController extends Controller
{
    public function index()
    {
        $coupon = Coupon::orderBy('id', 'desc')->get();
        return view('admin.coupons', compact('coupon'));
    }

    public function add_coupon() {
        return view('admin.coupon-add');
    }
    public function coupon_store(Request $request)
    {
        // @dd($request->all());
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'cart_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        // Coupon::create($request->all());
        Coupon::create([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_cart_value' => $request->cart_value ?? 0,
            'max_discount' => $request->max_discount ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_single_use' => $request->is_single_use == '1',
        ]);

        return redirect()->route('admin.coupons')->with('success', 'Coupon created successfully!');
        
    }

    public function coupon_edit() {
        $coupon = Coupon::findOrFail(request()->id);
        if (!$coupon) {
            return redirect()->route('admin.coupons')->with('error', 'Coupon not found.');
        }
        // Check if the coupon is already applied
        return view('admin.coupon-edit', compact('coupon'));
    }
    public function coupon_update(Request $request)
    {
        $coupon = Coupon::findOrFail($request->id);
        if (!$coupon) {
            return redirect()->route('admin.coupons')->with('error', 'Coupon not found.');
        }

        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'cart_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $coupon->update([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_cart_value' => $request->cart_value ?? 0,
            'max_discount' => $request->max_discount ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_single_use' => $request->is_single_use == '1',
        ]);

        return redirect()->route('admin.coupons')->with('success', 'Coupon updated successfully!');
        
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::findOrFail($id);
        if (!$coupon) {
            return redirect()->route('admin.coupons')->with('error', 'Coupon not found.');
        }
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('success', 'Coupon deleted successfully!');
    }

    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        $subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal())); 

        if ($subtotal < $coupon->min_cart_value) {
            return back()->with('error', "Minimum cart value should be ₹{$coupon->min_cart_value}.");
        }

        $discount = $coupon->type === 'fixed'
            ? $coupon->value
            : ($subtotal * $coupon->value / 100);

        session()->put('coupon', [ 
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount
        ]);
        
        return back()->with('success', 'Coupon applied successfully!');
    }
}
