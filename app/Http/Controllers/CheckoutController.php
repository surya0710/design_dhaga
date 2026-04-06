<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Order;
use App\Models\Address;
use Razorpay\Api\Api;

class CheckoutController extends Controller
{
    protected $categories;

    public function __construct()
    {
        $this->categories = Category::where('status', 1)
            ->where(function ($query) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', 0);
            })
            ->with('children')
            ->get();
    }

    public function checkout()
    {
        $cartItems = collect(Session::get('cart', []));

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $shipping = 0;
        $total = $subtotal + $shipping;
        $categories = $this->categories;

        $defaultAddress = null;

        if (auth()->check()) {
            $defaultAddress = Address::where('user_id', auth()->id())
                ->where('is_default', true)
                ->first();

            if (!$defaultAddress) {
                $defaultAddress = Address::where('user_id', auth()->id())
                    ->latest()
                    ->first();
            }
        }

        return view('frontend.checkout', compact(
            'cartItems',
            'subtotal',
            'shipping',
            'total',
            'categories',
            'defaultAddress'
        ));
    }

    public function createRazorpayOrder(Request $request)
    {
        $cartItems = collect(Session::get('cart', []));

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.'
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $subtotal = $cartItems->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $shipping = 0;
        $couponDiscount = 0;
        $total = $subtotal + $shipping - $couponDiscount;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => null,

                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],

                'country' => 'India',
                'state' => $validated['state'],
                'city' => $validated['city'],
                'pincode' => $validated['pincode'],
                'address_line_1' => $validated['address'],
                'address_line_2' => null,
                'landmark' => null,
                'address_type' => 'home',

                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'coupon_discount' => $couponDiscount,
                'total' => $total,

                'coupon_code' => null,
                'coupon_id' => null,

                'payment_method' => 'razorpay',
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item['id'] ?? null,
                    'product_name' => $item['name'],
                    'product_image' => $item['image'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $razorpayOrder = $api->order->create([
                'receipt' => 'order_' . $order->id,
                'amount' => (int) round($total * 100),
                'currency' => 'INR'
            ]);

            $order->update([
                'razorpay_order_id' => $razorpayOrder['id']
            ]);

            DB::commit();

            return response()->json([
                'key' => env('RAZORPAY_KEY'),
                'amount' => $razorpayOrder['amount'],
                'currency' => $razorpayOrder['currency'],
                'razorpay_order_id' => $razorpayOrder['id'],
                'local_order_id' => $order->id,
                'customer' => [
                    'name' => $order->name,
                    'email' => $order->email,
                    'phone' => $order->phone,
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyRazorpayPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'local_order_id' => 'required|integer',
        ]);

        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);

            $order = Order::findOrFail($request->local_order_id);

            $order->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
                'paid_at' => now(),
            ]);

            Session::forget('cart');

            return response()->json([
                'status' => true,
                'redirect_url' => route('home')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed.'
            ], 400);
        }
    }
}