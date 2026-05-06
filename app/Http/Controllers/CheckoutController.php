<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\Order;
use App\Models\Address;
use App\Models\Cart;
use App\Models\HaryanaPincode;
use Razorpay\Api\Api;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\OrderCompletedMail;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $categories;

    // Registered business state
    protected string $companyState = 'Haryana';
    protected float $gstRate = 5;

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
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return ((float) $item['price']) * ((int) $item['quantity']);
        });

        $shipping = 0;
        $couponDiscount = 0;
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

        $state = $defaultAddress->state ?? null;

        $taxableAmount = max($subtotal - $couponDiscount, 0);
        $gstData = $this->calculateGstBreakup($taxableAmount, $state);
        $total = $taxableAmount + $shipping + $gstData['gst_amount'];

        $categories = $this->categories;

        $coupon = session()->get('coupon');
        if ($coupon) {
            $couponDiscount = $coupon['discount'];
            $total = $total - $couponDiscount;
        }

        return view('frontend.checkout', compact(
            'cartItems',
            'subtotal',
            'shipping',
            'couponDiscount',
            'total',
            'categories',
            'defaultAddress',
            'gstData',
            'coupon'
        ));
    }

    public function getDeliveryOptions(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $cartItems = collect(Cart::with('product')
            ->where('user_id', Auth::id())
            ->get());

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.'
            ], 422);
        }

        $pickupPincode = config('services.shiprocket.pickup_pincode');

        if (empty($pickupPincode)) {
            return response()->json([
                'message' => 'Shiprocket pickup pincode is not configured.'
            ], 500);
        }

        $deliveryPincode = trim($validated['pincode']);

        $weight = max($cartItems->sum(function ($item) {
            $itemWeight = isset($item['weight']) ? (float) $item['weight'] : 0.5;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 1;
            return $itemWeight * $quantity;
        }), 0.5);

        $declaredValue = $cartItems->sum(function ($item) {
            $price = isset($item['price']) ? (float) $item['price'] : 0;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 1;
            return $price * $quantity;
        });

        try {
            $token = $this->getShiprocketToken();

            $response = Http::withToken($token)
                ->acceptJson()
                ->get('https://apiv2.shiprocket.in/v1/external/courier/serviceability/', [
                    'pickup_postcode' => $pickupPincode,
                    'delivery_postcode' => $deliveryPincode,
                    'cod' => 0,
                    'weight' => $weight,
                    'declared_value' => $declaredValue,
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Unable to fetch delivery options from Shiprocket.',
                    'error' => config('app.debug') ? $response->body() : null,
                ], 500);
            }

            $couriers = collect($response->json('data.available_courier_companies', []))
                ->filter(function ($item) {
                    return isset($item['courier_company_id']);
                })
                ->values();

            if ($couriers->isEmpty()) {
                return response()->json([
                    'message' => 'No delivery options available for this pincode.'
                ], 422);
            }

            // Regular = cheapest available
            $regular = $couriers
                ->sortBy(function ($item) {
                    return (float) ($item['rate'] ?? $item['freight_charge'] ?? PHP_FLOAT_MAX);
                })
                ->first();

            // Express = fastest available
            $express = $couriers
                ->sortBy(function ($item) {
                    return (int) ($item['estimated_delivery_days'] ?? PHP_INT_MAX);
                })
                ->first();

            return response()->json([
                'regular' => $regular ? $this->formatDeliveryOption($regular, 'regular') : null,
                'express' => $express ? $this->formatDeliveryOption($express, 'express') : null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Unable to fetch delivery options.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function createRazorpayOrder(Request $request)
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

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
            'delivery_type' => 'required|in:regular,express',
            'shiprocket_courier_id' => 'required',
        ]);

        $subtotal = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);
        $couponDiscount = 0;
        $taxableAmount = max($subtotal - $couponDiscount, 0);

        try {
            $deliveryOptions = $this->fetchDeliveryOptionsFromShiprocket(
                $validated['pincode'],
                $cartItems
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Unable to validate delivery option.'
            ], 500);
        }

        // ✅ IMPORTANT: Match selected courier dynamically (handles all key variations)
        $selectedOption = collect($deliveryOptions)->first(function ($option) use ($validated) {

            $optionCourierId =
                $option['courier_id']
                ?? $option['courier_company_id']
                ?? $option['id']
                ?? null;

            return (string)$optionCourierId === (string)$validated['shiprocket_courier_id'];
        });

        // ❌ If still not found → debug response
        if (!$selectedOption) {
            return response()->json([
                'message' => 'Delivery option not matched.',
                'debug' => $deliveryOptions // remove after testing
            ], 422);
        }

        // ✅ Extract delivery charge safely
        $shipping = (float)(
            $selectedOption['charge']
            ?? $selectedOption['rate']
            ?? $selectedOption['shipping_amount']
            ?? 0
        );

        // ✅ Extract ETA safely
        $deliveryEta =
            $selectedOption['etd']
            ?? $selectedOption['estimated_delivery_days']
            ?? $selectedOption['delivery_days']
            ?? null;

        // ✅ Extract courier name safely
        $courierName =
            $selectedOption['courier_name']
            ?? $selectedOption['courier_company']
            ?? null;

        // ✅ Label (based on selection)
        $deliveryLabel =
            $validated['delivery_type'] === 'express'
                ? 'Fastest'
                : 'Economical';

        // ✅ Convert ETA → expected date
        $expectedDate = null;
        if (!empty($deliveryEta) && preg_match('/\d+/', $deliveryEta, $matches)) {
            $expectedDate = now()->addDays((int)$matches[0]);
        }

        // GST
        $gstData = $this->calculateGstBreakup($taxableAmount, $validated['state']);
        $gstAmount = $gstData['gst_amount'];

        $total = $taxableAmount + $shipping + $gstAmount;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),

                // customer
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],

                // address
                'country' => 'India',
                'state' => $validated['state'],
                'city' => $validated['city'],
                'pincode' => $validated['pincode'],
                'address_line_1' => $validated['address'],

                // pricing
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'delivery_charge' => $shipping,

                // ✅ DELIVERY DETAILS (FIXED)
                'delivery_type' => $validated['delivery_type'],
                'delivery_eta' => $deliveryEta,
                'expected_delivery_date' => $expectedDate,
                'courier_name' => $courierName,
                'delivery_label' => $deliveryLabel,
                'shiprocket_courier_id' => (string)$validated['shiprocket_courier_id'],

                // GST
                'gst_amount' => $gstAmount,
                'gst_type' => $gstData['gst_type'],
                'cgst_amount' => $gstData['cgst_amount'],
                'sgst_amount' => $gstData['sgst_amount'],
                'igst_amount' => $gstData['igst_amount'],

                'total' => $total,

                'payment_method' => 'razorpay',
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product->id ?? null,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->image ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $razorpayOrder = $api->order->create([
                'receipt' => 'order_' . $order->id,
                'amount' => (int)round($total * 100),
                'currency' => 'INR'
            ]);

            $order->update([
                'razorpay_order_id' => $razorpayOrder['id']
            ]);

            DB::commit();

            return response()->json([
                'key' => config('services.razorpay.key'),
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

            $order = Order::with('items')->findOrFail($request->local_order_id);

            $order->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
                'paid_at' => now(),
            ]);

            $pdf = Pdf::loadView('pdf.invoice', [
                'order' => $order,
                'companyState' => $this->companyState,
            ]);

            if (!empty($order->email)) {
                Mail::to($order->email)->send(
                    new OrderCompletedMail($order, $pdf->output(), $this->companyState)
                );
            }

            Session::forget('cart');

            return response()->json([
                'status' => true,
                'redirect_url' => route('home')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 400);
        }
    }

    private function calculateGstBreakup(float $amount, ?string $customerState): array
    {
        $customerState = $this->normalizeState($customerState);
        $companyState = $this->normalizeState($this->companyState);

        $gstRate = 5.0;
        $cgstRate = 0.0;
        $sgstRate = 0.0;
        $igstRate = 0.0;

        $cgstAmount = 0.0;
        $sgstAmount = 0.0;
        $igstAmount = 0.0;

        $gstType = 'igst';

        if (!empty($customerState) && $customerState === $companyState) {
            $gstType = 'cgst_sgst';
            $cgstRate = 2.5;
            $sgstRate = 2.5;

            $cgstAmount = round(($amount * $cgstRate) / 100, 2);
            $sgstAmount = round(($amount * $sgstRate) / 100, 2);
        } else {
            $gstType = 'igst';
            $igstRate = 5.0;

            $igstAmount = round(($amount * $igstRate) / 100, 2);
        }

        return [
            'gst_type' => $gstType,
            'gst_rate' => $gstRate,
            'cgst_rate' => $cgstRate,
            'sgst_rate' => $sgstRate,
            'igst_rate' => $igstRate,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => $igstAmount,
            'gst_amount' => round($cgstAmount + $sgstAmount + $igstAmount, 2),
        ];
    }

    private function normalizeState(?string $state): string
    {
        return strtolower(trim((string) $state));
    }

    private function getShiprocketToken(): string
    {
        $response = Http::acceptJson()->post(
            'https://apiv2.shiprocket.in/v1/external/auth/login',
            [
                'email' => config('services.shiprocket.email'),
                'password' => config('services.shiprocket.password'),
            ]
        );

        if (!$response->successful() || empty($response->json('token'))) {
            throw new \Exception('Unable to authenticate with Shiprocket.');
        }

        return $response->json('token');
    }

    private function fetchDeliveryOptionsFromShiprocket(string $deliveryPincode, $cartItems): array
    {
        $pickupPincode = config('services.shiprocket.pickup_pincode');

        if (empty($pickupPincode)) {
            throw new \Exception('Shiprocket pickup pincode is not configured.');
        }

        $weight = max($cartItems->sum(function ($item) {
            $itemWeight = isset($item['weight']) ? (float) $item['weight'] : 0.5;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 1;
            return $itemWeight * $quantity;
        }), 0.5);

        $declaredValue = $cartItems->sum(function ($item) {
            $price = isset($item['price']) ? (float) $item['price'] : 0;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 1;
            return $price * $quantity;
        });

        $token = $this->getShiprocketToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->get('https://apiv2.shiprocket.in/v1/external/courier/serviceability/', [
                'pickup_postcode' => $pickupPincode,
                'delivery_postcode' => trim($deliveryPincode),
                'cod' => 0,
                'weight' => $weight,
                'declared_value' => $declaredValue,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Unable to fetch delivery options from Shiprocket.');
        }

        $couriers = collect($response->json('data.available_courier_companies', []))
            ->filter(function ($item) {
                return isset($item['courier_company_id']);
            })
            ->values();

        if ($couriers->isEmpty()) {
            throw new \Exception('No delivery options available for this pincode.');
        }

        $regular = $couriers
            ->sortBy(function ($item) {
                return (float) ($item['rate'] ?? $item['freight_charge'] ?? PHP_FLOAT_MAX);
            })
            ->first();

        $express = $couriers
            ->sortBy(function ($item) {
                return (int) ($item['estimated_delivery_days'] ?? PHP_INT_MAX);
            })
            ->first();

        return [
            'regular' => $regular ? $this->formatDeliveryOption($regular, 'regular') : null,
            'express' => $express ? $this->formatDeliveryOption($express, 'express') : null,
        ];
    }

    private function formatDeliveryOption(array $courier, string $type): array
    {
        return [
            'type' => $type,
            'charge' => (float) ($courier['rate'] ?? $courier['freight_charge'] ?? 0),
            'courier_id' => (string) ($courier['courier_company_id'] ?? ''),
            'label' => $courier['courier_name'] ?? ucfirst($type) . ' Delivery',
            'etd' => $courier['estimated_delivery_days'] ?? null,
        ];
    }

    public function invoice($id, $type = 'view')
    {
        $order = Order::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('user.invoice', compact('order'))
            ->setPaper('A4', 'portrait');

        // OPEN IN BROWSER (like Myntra)
        if ($type === 'view') {
            return $pdf->stream('invoice-'.$order->id.'.pdf');
        }

        // DOWNLOAD PDF
        return $pdf->download('invoice-'.$order->id.'.pdf');
    }

    public function calculateGst(Request $request)
    {
        $request->validate([
            'pincode' => 'required|digits:6',
            'taxable_amount' => 'required|numeric|min:0'
        ]);

        $pincode = $request->pincode;
        $taxableAmount = $request->taxable_amount;

        // ✅ Check from DB (BEST METHOD)
        $isHaryana = HaryanaPincode::where('pincode', $pincode)->exists();

        // ⚠️ Optional fallback (can remove if DB is complete)
        if (!$isHaryana && $pincode >= 120001 && $pincode <= 136156) {
            $isHaryana = true;
        }

        if ($isHaryana) {
            $cgstRate = 2.5;
            $sgstRate = 2.5;

            $cgstAmount = ($taxableAmount * $cgstRate) / 100;
            $sgstAmount = ($taxableAmount * $sgstRate) / 100;

            return response()->json([
                'gst_type' => 'cgst_sgst',
                'cgst_rate' => $cgstRate,
                'sgst_rate' => $sgstRate,
                'cgst_amount' => round($cgstAmount, 2),
                'sgst_amount' => round($sgstAmount, 2),
                'gst_amount' => round($cgstAmount + $sgstAmount, 2),
            ]);
        }

        // IGST
        $igstRate = 5;
        $igstAmount = ($taxableAmount * $igstRate) / 100;

        return response()->json([
            'gst_type' => 'igst',
            'igst_rate' => $igstRate,
            'igst_amount' => round($igstAmount, 2),
            'gst_amount' => round($igstAmount, 2),
        ]);
    }
}