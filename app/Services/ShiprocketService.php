<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    /*
    |--------------------------------------------------------------------------
    | AUTH TOKEN
    |--------------------------------------------------------------------------
    */

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
                throw new \RuntimeException('Shiprocket token not found.');
            }

            return $data['token'];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK SERVICEABILITY
    |--------------------------------------------------------------------------
    */

    public function checkServiceability(
        string $pickupPincode,
        string $deliveryPincode,
        float $weight,
        int $cod = 0,
        string $deliveryType = 'regular',
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

        // Retry once if token expired
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

                'courier_name'       => $courier['courier_name'] ?? null,
                'courier_company_id' => $courier['courier_company_id'] ?? null,

                'freight_charge' => $courier['freight_charge'] ?? null,
                'cod_charge'     => $courier['cod_charge'] ?? null,

                'total_charge' =>
                    $courier['rate']
                    ?? $courier['freight_charge']
                    ?? null,

                'estimated_delivery_days' =>
                    $courier['estimated_delivery_days']
                    ?? $courier['etd']
                    ?? $courier['delivery_days']
                    ?? null,

                'transport_mode' =>
                    strtolower($courier['transport_mode'] ?? ''),

                'courier_type' =>
                    strtolower($courier['courier_type'] ?? ''),

                'is_cod' => $courier['cod'] ?? 0,

                'raw' => $courier,
            ];

        })->values()->all();

        return [

            'serviceable' => count($normalized) > 0,

            'pickup_postcode'   => $pickupPincode,
            'delivery_postcode' => $deliveryPincode,

            'couriers' => $normalized,

            'best_option' => $this->pickBestCourier(
                $normalized,
                $deliveryType,
                $cod
            ),

            'raw_response' => $json,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PICK BEST COURIER
    |--------------------------------------------------------------------------
    */

    protected function pickBestCourier(
        array $couriers,
        string $deliveryType = 'regular',
        int $cod = 0
    ): ?array {

        if (empty($couriers)) {
            return null;
        }

        $couriers = collect($couriers);

        /*
        |--------------------------------------------------------------------------
        | COD FILTER
        |--------------------------------------------------------------------------
        */

        if ($cod === 1) {

            $codCouriers = $couriers
                ->filter(fn ($courier) => ($courier['is_cod'] ?? 0) == 1)
                ->values();

            if ($codCouriers->isNotEmpty()) {
                $couriers = $codCouriers;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DELIVERY TYPE FILTER
        |--------------------------------------------------------------------------
        */

        if ($deliveryType === 'express') {

            // Prefer AIR
            $airCouriers = $couriers->filter(function ($courier) {

                return
                    str_contains($courier['transport_mode'] ?? '', 'air') ||
                    str_contains($courier['courier_type'] ?? '', 'air');

            })->values();

            if ($airCouriers->isNotEmpty()) {
                $couriers = $airCouriers;
            }

        } else {

            // Prefer SURFACE
            $surfaceCouriers = $couriers->filter(function ($courier) {

                return
                    str_contains($courier['transport_mode'] ?? '', 'surface') ||
                    str_contains($courier['courier_type'] ?? '', 'surface');

            })->values();

            if ($surfaceCouriers->isNotEmpty()) {
                $couriers = $surfaceCouriers;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */

        $couriers = $couriers->sort(function ($a, $b) use ($deliveryType) {

            // EXPRESS = FASTEST
            if ($deliveryType === 'express') {

                $daysA = is_numeric($a['estimated_delivery_days'])
                    ? (float) $a['estimated_delivery_days']
                    : 9999;

                $daysB = is_numeric($b['estimated_delivery_days'])
                    ? (float) $b['estimated_delivery_days']
                    : 9999;

                if ($daysA !== $daysB) {
                    return $daysA <=> $daysB;
                }
            }

            // REGULAR = CHEAPEST

            $priceA = is_numeric($a['total_charge'])
                ? (float) $a['total_charge']
                : 999999;

            $priceB = is_numeric($b['total_charge'])
                ? (float) $b['total_charge']
                : 999999;

            return $priceA <=> $priceB;

        })->values();

        return $couriers->first();
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ORDER
    |--------------------------------------------------------------------------
    */

    public function createOrder($order, array $package = []): array
    {
        $token = $this->getToken();

        $items = [];

        foreach ($order->items as $item) {

            $items[] = [
                'name'          => $item->product_name,
                'sku'           => 'SKU-' . $item->id,
                'units'         => $item->quantity,
                'selling_price' => $item->price,
            ];
        }

        $payload = [

            "order_id"   => (string) $order->id . '-' . time(),
            "order_date" => now()->format('Y-m-d H:i'),

            // Dynamic pickup location
            "pickup_location" =>
                $package['pickup_location']
                ?? config('services.shiprocket.pickup_location', 'Home'),

            "billing_customer_name" => $order->name,
            "billing_last_name"     => "",

            "billing_address" => $order->address_line_1,
            "billing_city"    => $order->city,
            "billing_pincode" => $order->pincode,
            "billing_state"   => $order->state,
            "billing_country" => $order->country ?? "India",

            "billing_email" => $order->email,
            "billing_phone" => $order->phone,

            "shipping_is_billing" => true,

            "order_items" => $items,

            "payment_method" =>
                strtoupper($order->payment_method) === 'COD'
                ? 'COD'
                : 'Prepaid',

            "sub_total" => $order->total,

            "length"  => (float) ($package['length'] ?? 10),
            "breadth" => (float) ($package['breadth'] ?? 10),
            "height"  => (float) ($package['height'] ?? 10),

            // KG ONLY
            "weight" => (float) ($package['weight'] ?? 0.5),
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

        Log::info('Shiprocket createOrder response', [
            'order_id' => $order->id,
            'response' => $data,
        ]);

        if (empty($data['shipment_id'])) {

            throw new \RuntimeException(
                'Shiprocket order creation failed: ' .
                json_encode($data)
            );
        }

        return [
            'order_id'    => $data['order_id'],
            'shipment_id' => $data['shipment_id'],
            'raw'         => $data,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN COURIER
    |--------------------------------------------------------------------------
    */

    public function assignCourier(
        int $shipmentId,
        int $courierId
    ): array {

        $token = $this->getToken();

        $response = Http::timeout(30)
            ->withToken($token)
            ->acceptJson()
            ->post("{$this->baseUrl}/courier/assign/awb", [
                'shipment_id' => $shipmentId,
                'courier_id'  => $courierId,
            ]);

        if ($response->status() === 401) {

            Cache::forget('shiprocket_token');

            $token = $this->getToken();

            $response = Http::timeout(30)
                ->withToken($token)
                ->acceptJson()
                ->post("{$this->baseUrl}/courier/assign/awb", [
                    'shipment_id' => $shipmentId,
                    'courier_id'  => $courierId,
                ]);
        }

        $response->throw();

        $data = $response->json();

        if (($data['awb_assign_status'] ?? 0) !== 1) {

            $error =
                $data['response']['data']['awb_assign_error']
                ?? json_encode($data);

            throw new \RuntimeException($error);
        }

        $awbData = $data['response']['data'] ?? [];

        if (empty($awbData['awb_code'])) {

            throw new \RuntimeException(
                'AWB missing in response.'
            );
        }

        return [

            'awb_code' =>
                $awbData['awb_code'] ?? null,

            'courier_name' =>
                $awbData['courier_name'] ?? null,

            'raw' => $data,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | TRACK ORDER
    |--------------------------------------------------------------------------
    */

    public function trackOrder(string $awbCode): array
    {
        $token = $this->getToken();

        $response = Http::timeout(30)
            ->withToken($token)
            ->acceptJson()
            ->get("{$this->baseUrl}/courier/track/awb/{$awbCode}");

        if ($response->status() === 401) {

            Cache::forget('shiprocket_token');

            $token = $this->getToken();

            $response = Http::timeout(30)
                ->withToken($token)
                ->acceptJson()
                ->get("{$this->baseUrl}/courier/track/awb/{$awbCode}");
        }

        $response->throw();

        $data = $response->json();

        $tracking = $data['tracking_data'] ?? [];

        return [

            'current_status' =>
                $tracking['shipment_track'][0]['current_status'] ?? 'N/A',

            'courier_name' =>
                $tracking['shipment_track'][0]['courier_name'] ?? 'N/A',

            'awb_code' =>
                $tracking['shipment_track'][0]['awb_code'] ?? $awbCode,

            'eta' =>
                $tracking['shipment_track'][0]['etd'] ?? null,

            'origin' =>
                $tracking['shipment_track'][0]['origin'] ?? 'N/A',

            'destination' =>
                $tracking['shipment_track'][0]['destination'] ?? 'N/A',

            'delivered_date' =>
                $tracking['shipment_track'][0]['delivered_date'] ?? null,

            'activities' =>
                $tracking['shipment_track_activities'] ?? [],

            'raw' => $data,
        ];
    }
}