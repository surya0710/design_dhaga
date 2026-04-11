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
}