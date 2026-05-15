<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

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
            'end_date' => 'required|date|after_or_equal:start_date',
            'free_shipping' => 'required'
        ]);
        Coupon::create([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_cart_value' => $request->cart_value ?? 0,
            'max_discount' => $request->max_discount ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_single_use' => $request->is_single_use == '1',
            'free_shipping' => $request->free_shipping
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
            'end_date' => 'required|date|after_or_equal:start_date',
            'free_shipping' => 'required'
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
            'free_shipping' => $request->free_shipping
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
            'code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        // =========================
        // Coupon Exists
        // =========================
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        // =========================
        // Status Check
        // =========================
        if (!$coupon->status) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is inactive'
            ]);
        }

        $now = now();

        // =========================
        // Start Date Check
        // =========================
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not active yet'
            ]);
        }

        // =========================
        // Expiry Check
        // =========================
        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has expired'
            ]);
        }

        // =========================
        // Get Cart
        // =========================
        $cartItems = Cart::where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ]);
        }

        // =========================
        // Calculate Subtotal
        // =========================
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // =========================
        // Minimum Cart Value Check
        // =========================
        if (
            $coupon->min_cart_value &&
            $subtotal < $coupon->min_cart_value
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum cart value should be ₹' . number_format($coupon->min_cart_value, 2)
            ]);
        }

        // =========================
        // Usage Limit Check
        // =========================
        if (
            $coupon->usage_limit &&
            $coupon->used_count >= $coupon->usage_limit
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon usage limit exceeded'
            ]);
        }

        // =========================
        // Single Use Per User Check
        // =========================
        if ($coupon->is_single_use) {

            $alreadyUsed = Order::where('user_id', Auth::id())
                ->where('coupon_code', $coupon->code)
                ->exists();

            if ($alreadyUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this coupon'
                ]);
            }
        }

        // =========================
        // Discount Calculation
        // =========================
        $discount = 0;
        $freeShipping = false;

        // Free Shipping Coupon
        if (
            $coupon->free_shipping ||
            $coupon->type === 'shipping'
        ) {

            $freeShipping = true;

        } else {

            // Fixed Discount
            if ($coupon->type === 'fixed') {

                $discount = $coupon->value;

            } 
            // Percentage Discount
            else {

                $discount = ($subtotal * $coupon->value) / 100;
            }

            // Max Discount Cap
            if (
                $coupon->max_discount &&
                $discount > $coupon->max_discount
            ) {
                $discount = $coupon->max_discount;
            }

            // Prevent Over Discount
            $discount = min($discount, $subtotal);
        }

        // =========================
        // Store Coupon In Session
        // =========================
        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => round($discount, 2),
            'free_shipping' => $freeShipping
        ]);

        return response()->json([
            'success' => true,
            'message' => $freeShipping
                ? 'Free shipping applied successfully'
                : 'Coupon applied successfully',
            'coupon_code' => $coupon->code,
            'discount' => round($discount, 2),
            'free_shipping' => $freeShipping,
            'subtotal' => round($subtotal, 2),
            'final_total' => round($subtotal - $discount, 2)
        ]);
    }

    public function remove()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed successfully!');
    }
}
