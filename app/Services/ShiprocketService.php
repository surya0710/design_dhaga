<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ShiprocketService
{
    protected string $baseUrl;
    protected string $email;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.shiprocket.base_url'), '/');
        $this->email = config('services.shiprocket.email');
        $this->password = config('services.shiprocket.password');
    }

    public function getToken(): string
    {
        return Cache::remember('shiprocket_token', now()->addMinutes(8), function () {
            $response = Http::timeout(20)
                ->acceptJson()
                ->post("{$this->baseUrl}/auth/login", [
                    'email' => $this->email,
                    'password' => $this->password,
                ]);

            $response->throw();

            $data = $response->json();

            if (empty($data['token'])) {
                throw new \RuntimeException('Shiprocket token not found in response.');
            }

            return $data['token'];
        });
    }

    public function checkServiceability(
        string $pickupPincode = '400013',
        string $deliveryPincode,
        float $weight,
        int $cod = 0,
        ?float $length = null,
        ?float $breadth = null,
        ?float $height = null,
        ?float $declaredValue = null
    ): array {
        $token = $this->getToken();

        $query = array_filter([
            'pickup_postcode'   => $pickupPincode,
            'delivery_postcode' => $deliveryPincode,
            'cod'               => $cod,
            'weight'            => $weight,
            'length'            => $length,
            'breadth'           => $breadth,
            'height'            => $height,
            'declared_value'    => $declaredValue,
        ], fn ($value) => $value !== null && $value !== '');

        $response = Http::timeout(30)
            ->withToken($token)
            ->acceptJson()
            ->get("{$this->baseUrl}/courier/serviceability/", $query);

        if ($response->status() === 401) {
            Cache::forget('shiprocket_token');
            $token = $this->getToken();

            $response = Http::timeout(30)
                ->withToken($token)
                ->acceptJson()
                ->get("{$this->baseUrl}/courier/serviceability/", $query);
        }

        $response->throw();

        $json = $response->json();
        $couriers = data_get($json, 'data.available_courier_companies', []);

        $normalized = collect($couriers)->map(function ($courier) {
            return [
                'courier_name' => $courier['courier_name'] ?? null,
                'courier_company_id' => $courier['courier_company_id'] ?? null,
                'freight_charge' => $courier['freight_charge'] ?? null,
                'cod_charge' => $courier['cod_charge'] ?? null,
                'total_charge' => $courier['rate'] ?? ($courier['freight_charge'] ?? null),
                'estimated_delivery_days' =>
                    $courier['estimated_delivery_days']
                    ?? $courier['etd']
                    ?? $courier['delivery_days']
                    ?? null,
                'raw' => $courier,
            ];
        })->values()->all();

        return [
            'serviceable' => count($normalized) > 0,
            'pickup_postcode' => $pickupPincode,
            'delivery_postcode' => $deliveryPincode,
            'couriers' => $normalized,
            'best_option' => $this->pickBestCourier($normalized),
            'raw_response' => $json,
        ];
    }

    protected function pickBestCourier(array $couriers): ?array
    {
        if (empty($couriers)) {
            return null;
        }

        usort($couriers, function ($a, $b) {
            $daysA = is_numeric($a['estimated_delivery_days']) ? (float) $a['estimated_delivery_days'] : 9999;
            $daysB = is_numeric($b['estimated_delivery_days']) ? (float) $b['estimated_delivery_days'] : 9999;

            if ($daysA !== $daysB) {
                return $daysA <=> $daysB;
            }

            $priceA = is_numeric($a['total_charge']) ? (float) $a['total_charge'] : 999999;
            $priceB = is_numeric($b['total_charge']) ? (float) $b['total_charge'] : 999999;

            return $priceA <=> $priceB;
        });

        return $couriers[0];
    }

    public function createOrder($order, array $package = []): array
    {
        $token = $this->getToken();

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'name' => $item->product_name,
                'sku' => 'SKU-' . $item->id,
                'units' => $item->quantity,
                'selling_price' => $item->price,
            ];
        }

        $weightInGrams = (float) ($package['weight'] ?? 500);

        // Convert to kg for Shiprocket
        $weightInKg = $weightInGrams / 1000;

        $payload = [
            "order_id" => (string) $order->id,
            "order_date" => now()->format('Y-m-d H:i'),
            "pickup_location" => config('services.shiprocket.pickup_location', 'Home'),

            "billing_customer_name" => $order->name,
            "billing_last_name" => "",
            "billing_address" => $order->address_line_1,
            "billing_city" => $order->city,
            "billing_pincode" => $order->pincode,
            "billing_state" => $order->state,
            "billing_country" => $order->country ?? "India",
            "billing_email" => $order->email,
            "billing_phone" => $order->phone,

            "shipping_is_billing" => true,

            "order_items" => $items,

            "payment_method" => strtoupper($order->payment_method) === 'COD' ? 'COD' : 'Prepaid',

            "sub_total" => $order->total,

            // ✅ Dynamic package details
            "length"  => $package['length'] ?? 10,
            "breadth" => $package['breadth'] ?? 10,
            "height"  => $package['height'] ?? 10,
            "weight"  => $weightInKg ?? 0.5,
        ];

        $response = Http::timeout(30)
            ->withToken($token)
            ->acceptJson()
            ->post("{$this->baseUrl}/orders/create/adhoc", $payload);

        if ($response->status() === 401) {
            Cache::forget('shiprocket_token');
            $token = $this->getToken();

            $response = Http::timeout(30)
                ->withToken($token)
                ->acceptJson()
                ->post("{$this->baseUrl}/orders/create/adhoc", $payload);
        }

        $response->throw();

        $data = $response->json();

        if (empty($data['shipment_id'])) {
            throw new \RuntimeException('Shiprocket order creation failed: ' . json_encode($data));
        }

        return [
            'order_id' => $data['order_id'],
            'shipment_id' => $data['shipment_id'],
            'raw' => $data
        ];
    }

    public function trackShipment($awb)
    {
        $token = $this->getToken();

        $response = Http::withToken($token)->get("{$this->baseUrl}/courier/track/awb/{$awb}");

        $response->throw();

        $data = $response->json();

        return $data['tracking_data'] ?? [];
    }

    public function assignCourier(int $shipmentId, ?int $courierId = null): array
    {
        $token = $this->getToken();

        $payload = [
            "shipment_id" => $shipmentId,
        ];

        // Optional: force a specific courier
        if ($courierId) {
            $payload["courier_id"] = $courierId;
        }

        $response = Http::timeout(30)
            ->withToken($token)
            ->acceptJson()
            ->post("{$this->baseUrl}/courier/assign/awb", $payload);

        // Retry on token expiry
        if ($response->status() === 401) {
            Cache::forget('shiprocket_token');
            $token = $this->getToken();

            $response = Http::timeout(30)
                ->withToken($token)
                ->acceptJson()
                ->post("{$this->baseUrl}/courier/assign/awb", $payload);
        }

        $response->throw();

        $data = $response->json();

        if (empty($data['awb_code'])) {
            throw new \RuntimeException('Courier assignment failed: ' . json_encode($data));
        }

        return [
            'awb_code' => $data['awb_code'],
            'courier_name' => $data['courier_name'] ?? null,
            'raw' => $data
        ];
    }
}