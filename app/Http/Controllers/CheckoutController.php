<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Address;
use Razorpay\Api\Api;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\OrderCompletedMail;

class CheckoutController extends Controller
{
    protected $categories;

    // Registered business state
    protected string $companyState = 'Haryana';

    // Change this as per your product GST slab: 5, 12, 18, 28 etc.
    protected float $gstRate = 18;

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
        $defaultAddress = null;

        if (auth()->check()) {
            $defaultAddress = Address::where('user_id', auth()->id())->where('is_default', true)->first();

            if (!$defaultAddress) {
                $defaultAddress = Address::where('user_id', auth()->id())->latest()->first();
            }
        }

        $state = $defaultAddress->state ?? null;
        $gstData = $this->calculateGstBreakup($subtotal, $state);

        $total = $subtotal + $shipping + $gstData['gst_amount'];

        $categories = $this->categories;

        return view('frontend.checkout', compact(
            'cartItems',
            'subtotal',
            'shipping',
            'total',
            'categories',
            'defaultAddress',
            'gstData'
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

        $gstData = $this->calculateGstBreakup($subtotal, $validated['state']);

        $taxableAmount = $subtotal - $couponDiscount;
        $gstAmount = $gstData['gst_amount'];

        $total = $taxableAmount + $shipping + $gstAmount;

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

                // GST fields (add these columns in orders table if not present)
                'gst_rate' => $gstData['gst_rate'],
                'gst_type' => $gstData['gst_type'],
                'cgst_rate' => $gstData['cgst_rate'],
                'sgst_rate' => $gstData['sgst_rate'],
                'igst_rate' => $gstData['igst_rate'],
                'cgst_amount' => $gstData['cgst_amount'],
                'sgst_amount' => $gstData['sgst_amount'],
                'igst_amount' => $gstData['igst_amount'],
                'gst_amount' => $gstData['gst_amount'],

                'total' => $total,

                'coupon_code' => null,
                'coupon_id' => null,

                'payment_method' => 'razorpay',
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $lineSubtotal = $item['price'] * $item['quantity'];

                $order->items()->create([
                    'product_id' => $item['id'] ?? null,
                    'product_name' => $item['name'],
                    'product_image' => $item['image'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $lineSubtotal,
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
                'key' => config('services.razorpay.key'),
                'amount' => $razorpayOrder['amount'],
                'currency' => $razorpayOrder['currency'],
                'razorpay_order_id' => $razorpayOrder['id'],
                'local_order_id' => $order->id,
                'customer' => [
                    'name' => $order->name,
                    'email' => $order->email,
                    'phone' => $order->phone,
                ],
                'billing' => [
                    'subtotal' => round($subtotal, 2),
                    'shipping' => round($shipping, 2),
                    'gst_type' => $gstData['gst_type'],
                    'gst_rate' => $gstData['gst_rate'],
                    'cgst_amount' => round($gstData['cgst_amount'], 2),
                    'sgst_amount' => round($gstData['sgst_amount'], 2),
                    'igst_amount' => round($gstData['igst_amount'], 2),
                    'gst_amount' => round($gstData['gst_amount'], 2),
                    'total' => round($total, 2),
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

            // Generate PDF invoice
            $pdf = Pdf::loadView('pdf.invoice', [
                'order' => $order,
                'companyState' => $this->companyState,
            ]);

            // Send mail with attached PDF
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

        $gstRate = $this->gstRate;
        $cgstRate = 0;
        $sgstRate = 0;
        $igstRate = 0;
        $cgstAmount = 0;
        $sgstAmount = 0;
        $igstAmount = 0;
        $gstType = 'igst';

        if ($customerState === $companyState) {
            $gstType = 'cgst_sgst';
            $cgstRate = $gstRate / 2;
            $sgstRate = $gstRate / 2;
            $cgstAmount = round(($amount * $cgstRate) / 100, 2);
            $sgstAmount = round(($amount * $sgstRate) / 100, 2);
        } else {
            $gstType = 'igst';
            $igstRate = $gstRate;
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
}