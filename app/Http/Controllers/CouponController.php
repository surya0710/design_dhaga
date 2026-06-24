<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $coupon = Coupon::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('code', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhere('value', 'like', '%' . $search . '%')
                        ->orWhere('min_cart_value', 'like', '%' . $search . '%')
                        ->orWhere('max_discount', 'like', '%' . $search . '%')
                        ->orWhere('start_date', 'like', '%' . $search . '%')
                        ->orWhere('end_date', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

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

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        // ✅ Check status
        if ($coupon->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is inactive'
            ]);
        }

        $now = now();

        // ✅ Check start date
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not active yet'
            ]);
        }

        // ✅ Check expiry
        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has expired'
            ]);
        }

        // ✅ Get cart subtotal from database (same source as cart/checkout pages)
        $subtotal = (float) Cart::where('user_id', Auth::id())
            ->get()
            ->sum(function ($item) {
                return ((float) $item->price) * ((int) $item->quantity);
            });

        // ✅ Min cart check
        $minCartValue = (float) $coupon->min_cart_value;
        if ($minCartValue > 0 && $subtotal < $minCartValue) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum cart value should be ₹' . number_format($minCartValue, 2)
            ]);
        }

        $freeShipping = (isset($coupon->free_shipping) && $coupon->free_shipping)
            || $coupon->type === 'shipping';

        $discount = 0;

        if ($coupon->type === 'fixed') {
            $discount = (float) $coupon->value;
        } elseif ($coupon->type === 'percent') {
            $discount = ($subtotal * (float) $coupon->value) / 100;
        }

        if ($coupon->max_discount && $discount > (float) $coupon->max_discount) {
            $discount = (float) $coupon->max_discount;
        }

        $discount = min($discount, $subtotal);

        // =========================
        // ✅ SINGLE USE CHECK
        // =========================
        if ($coupon->is_single_use) {
            if (session()->has('coupon_used_' . $coupon->code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon already used'
                ]);
            }
        }

        // =========================
        // ✅ STORE IN SESSION
        // =========================
        session()->put('coupon', [
            'code' => $coupon->code,
            'discount' => round($discount, 2),
            'free_shipping' => $freeShipping
        ]);

        if ($coupon->is_single_use) {
            session()->put('coupon_used_' . $coupon->code, true);
        }

        if ($freeShipping && $discount > 0) {
            $message = 'Coupon applied: ₹' . number_format($discount, 2) . ' off + free shipping';
        } elseif ($freeShipping) {
            $message = 'Free shipping applied successfully';
        } else {
            $message = 'Coupon applied successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'discount' => round($discount, 2),
            'free_shipping' => $freeShipping
        ]);
    }

    public function remove()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed successfully!');
    }
}
