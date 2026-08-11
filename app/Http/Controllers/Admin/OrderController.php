<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    protected string $companyState = 'Haryana';

    public function create()
    {
        $products = Product::query()
            ->with(['variants' => function ($query) {
                $query->where('is_active', true)->orderBy('size')->orderBy('fabric_type');
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'sale_price', 'regular_price', 'image', 'weight']);

        $productsPayload = $products->map(function (Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) ($product->sale_price ?: $product->regular_price),
                'image' => $product->image,
                'weight' => Product::normalizeWeightToKg($product->weight ?? null),
                'variants' => $product->variants->map(function (ProductVariant $variant) {
                    return [
                        'id' => $variant->id,
                        'size' => $variant->size,
                        'fabric_type' => $variant->fabric_type,
                        'sku' => $variant->sku,
                        'price' => (float) $variant->price,
                    ];
                })->values(),
            ];
        })->values();

        $indianStates = $this->indianStates();

        return view('admin.order-add', compact('products', 'productsPayload', 'indianStates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'landmark' => 'nullable|string|max:255',
            'address_type' => 'nullable|in:home,work,other',
            'notes' => 'nullable|string|max:2000',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.sku' => 'nullable|string|max:100',
            'items.*.size' => 'nullable|string|max:100',
            'items.*.fabric_type' => 'nullable|string|max:100',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',

            'shipping_mode' => 'required|in:shiprocket,manual',
            'delivery_type' => 'required|in:regular,express,manual',
            'shiprocket_courier_id' => 'nullable|string|max:100',
            'shipping' => 'nullable|numeric|min:0',
            'courier_name' => 'nullable|string|max:255',
            'delivery_eta' => 'nullable|string|max:100',
            'delivery_label' => 'nullable|string|max:100',
            'expected_delivery_date' => 'nullable|date',

            'coupon_discount' => 'nullable|numeric|min:0',
            'payment_method' => ['required', Rule::in(['offline', 'cod', 'bank_transfer', 'razorpay'])],
            'payment_status' => ['required', Rule::in(['pending', 'paid'])],
            'order_status' => ['required', Rule::in(['pending', 'confirmed', 'packed'])],
            'link_user' => 'nullable|boolean',
        ]);

        if ($validated['shipping_mode'] === 'shiprocket') {
            $request->validate([
                'shiprocket_courier_id' => 'required|string|max:100',
                'delivery_type' => 'required|in:regular,express',
                'shipping' => 'required|numeric|min:0',
            ]);
        } else {
            $request->validate([
                'shipping' => 'required|numeric|min:0',
            ]);
        }

        $items = collect($validated['items'])->map(function (array $item) {
            $price = round((float) $item['price'], 2);
            $quantity = (int) $item['quantity'];

            return [
                'product_id' => $item['product_id'] ?? null,
                'product_variant_id' => $item['product_variant_id'] ?? null,
                'product_name' => $item['product_name'],
                'product_image' => null,
                'sku' => $item['sku'] ?? null,
                'size' => $item['size'] ?? null,
                'fabric_type' => $item['fabric_type'] ?? null,
                'price' => $price,
                'quantity' => $quantity,
                'total' => round($price * $quantity, 2),
            ];
        });

        // Enrich catalog products with image / defaults from DB
        $productIds = $items->pluck('product_id')->filter()->unique()->values();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variantIds = $items->pluck('product_variant_id')->filter()->unique()->values();
        $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        $items = $items->map(function (array $item) use ($products, $variants) {
            if (!empty($item['product_id']) && isset($products[$item['product_id']])) {
                $product = $products[$item['product_id']];
                $item['product_image'] = $product->image;
                if (empty($item['product_name'])) {
                    $item['product_name'] = $product->name;
                }
            }

            if (!empty($item['product_variant_id']) && isset($variants[$item['product_variant_id']])) {
                $variant = $variants[$item['product_variant_id']];
                $item['sku'] = $item['sku'] ?: $variant->sku;
                $item['size'] = $item['size'] ?: $variant->size;
                $item['fabric_type'] = $item['fabric_type'] ?: $variant->fabric_type;
            }

            return $item;
        });

        $subtotal = round($items->sum('total'), 2);
        $couponDiscount = round((float) ($validated['coupon_discount'] ?? 0), 2);
        $couponDiscount = min($couponDiscount, $subtotal);
        $taxableAmount = max($subtotal - $couponDiscount, 0);
        $shipping = round((float) $validated['shipping'], 2);

        $gstData = $this->calculateGstBreakup($taxableAmount, $validated['state']);
        $total = round($taxableAmount + $shipping + $gstData['gst_amount'], 2);

        $deliveryType = $validated['delivery_type'] === 'manual'
            ? 'regular'
            : $validated['delivery_type'];

        $deliveryLabel = $validated['delivery_label']
            ?? ($deliveryType === 'express' ? 'Fastest' : ($validated['shipping_mode'] === 'manual' ? 'Manual' : 'Economical'));

        $expectedDate = $validated['expected_delivery_date'] ?? null;
        if (!$expectedDate && !empty($validated['delivery_eta']) && preg_match('/\d+/', $validated['delivery_eta'], $matches)) {
            $expectedDate = now()->addDays((int) $matches[0])->toDateString();
        }

        $userId = null;
        if ($request->boolean('link_user')) {
            $userId = User::where('email', $validated['email'])->value('id');
        }

        $paymentStatus = $validated['payment_status'];
        $orderStatus = $validated['order_status'];
        $paidAt = $paymentStatus === 'paid' ? now() : null;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $userId,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country' => $validated['country'] ?? 'India',
                'state' => $validated['state'],
                'city' => $validated['city'],
                'pincode' => $validated['pincode'],
                'address_line_1' => $validated['address_line_1'],
                'address_line_2' => $validated['address_line_2'] ?? null,
                'landmark' => $validated['landmark'] ?? null,
                'address_type' => $validated['address_type'] ?? 'home',
                'notes' => $validated['notes'] ?? null,

                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'delivery_charge' => $shipping,
                'coupon_discount' => $couponDiscount,
                'total' => $total,

                'gst_rate' => $gstData['gst_rate'],
                'gst_amount' => $gstData['gst_amount'],
                'gst_type' => $gstData['gst_type'],
                'cgst_rate' => $gstData['cgst_rate'],
                'sgst_rate' => $gstData['sgst_rate'],
                'igst_rate' => $gstData['igst_rate'],
                'cgst_amount' => $gstData['cgst_amount'],
                'sgst_amount' => $gstData['sgst_amount'],
                'igst_amount' => $gstData['igst_amount'],

                'delivery_type' => $deliveryType,
                'delivery_eta' => $validated['delivery_eta'] ?? null,
                'expected_delivery_date' => $expectedDate,
                'courier_name' => $validated['courier_name'] ?? null,
                'delivery_label' => $deliveryLabel,
                'shiprocket_courier_id' => $validated['shiprocket_courier_id'] ?? null,

                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'paid_at' => $paidAt,
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            return redirect()
                ->route('admin.order.detail', $order->id)
                ->with('status', 'Order #' . $order->id . ' created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin order create failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->with('error', 'Unable to create order: ' . $e->getMessage());
        }
    }

    public function deliveryOptions(Request $request)
    {
        $validated = $request->validate([
            'pincode' => 'required|string|max:20',
            'weight' => 'nullable|numeric|min:0.1',
            'declared_value' => 'nullable|numeric|min:0',
            'cod' => 'nullable|boolean',
        ]);

        $pickupPincode = config('services.shiprocket.pickup_pincode');

        if (empty($pickupPincode)) {
            return response()->json([
                'message' => 'Shiprocket pickup pincode is not configured.',
            ], 500);
        }

        $weight = max((float) ($validated['weight'] ?? 0.5), 0.5);
        $declaredValue = (float) ($validated['declared_value'] ?? 0);
        $cod = $request->boolean('cod') ? 1 : 0;

        try {
            $token = $this->getShiprocketToken();

            $response = Http::withToken($token)
                ->acceptJson()
                ->get('https://apiv2.shiprocket.in/v1/external/courier/serviceability/', [
                    'pickup_postcode' => $pickupPincode,
                    'delivery_postcode' => trim($validated['pincode']),
                    'cod' => $cod,
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
                ->filter(fn ($item) => isset($item['courier_company_id']))
                ->values();

            if ($couriers->isEmpty()) {
                return response()->json([
                    'message' => 'No delivery options available for this pincode.',
                ], 422);
            }

            $regular = $couriers
                ->sortBy(fn ($item) => (float) ($item['rate'] ?? $item['freight_charge'] ?? PHP_FLOAT_MAX))
                ->first();

            $express = $couriers
                ->sortBy(fn ($item) => (int) ($item['estimated_delivery_days'] ?? PHP_INT_MAX))
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

    private function calculateGstBreakup(float $amount, ?string $customerState): array
    {
        $customerState = strtolower(trim((string) $customerState));
        $companyState = strtolower(trim($this->companyState));

        $gstRate = 5.0;
        $cgstRate = 0.0;
        $sgstRate = 0.0;
        $igstRate = 0.0;
        $cgstAmount = 0.0;
        $sgstAmount = 0.0;
        $igstAmount = 0.0;
        $gstType = 'igst';

        if ($customerState !== '' && $customerState === $companyState) {
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

    private function indianStates(): array
    {
        return [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
            'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka',
            'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram',
            'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu',
            'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
            'Andaman and Nicobar Islands', 'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry',
        ];
    }
}
