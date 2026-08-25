<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ShipmentTrackingSyncService
{
    public function __construct(private ShiprocketService $shiprocket)
    {
    }

    public function syncOpenOrders(): int
    {
        $orders = Order::whereNotNull('awb_code')
            ->where('awb_code', '!=', '')
            ->whereNotIn('order_status', ['delivered', 'cancelled'])
            ->get();

        Log::info('SyncShipmentTracking started', [
            'orders_count' => $orders->count(),
        ]);

        foreach ($orders as $order) {
            try {
                Log::info('SyncShipmentTracking tracking order', [
                    'order_id' => $order->id,
                    'awb_code' => $order->awb_code,
                ]);

                $tracking = $this->shiprocket->trackOrder($order->awb_code);

                if (! empty($tracking)) {
                    $this->applyTracking($order, $tracking);
                }
            } catch (\Throwable $e) {
                Log::error('Tracking failed', [
                    'order_id' => $order->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $orders->count();
    }

    public function applyTracking(Order $order, array $tracking): Order
    {
        $status = $tracking['current_status'] ?? $order->tracking_status;
        $deliveredDate = $tracking['delivered_date'] ?? null;

        $order->tracking_status = $status;
        $order->tracking_last_update = $deliveredDate ?: now();
        $order->tracking_raw_json = json_encode($tracking['raw'] ?? $tracking);

        $this->applyOrderStatus($order, $status, $deliveredDate);
        $order->save();

        return $order;
    }

    public function applyWebhookPayload(Order $order, array $data, ?string $rawJson = null): Order
    {
        $status = $data['current_status'] ?? $order->tracking_status;
        $deliveredDate = $data['delivered_date'] ?? $data['delivered_at'] ?? null;

        if (isset($data['current_status'])) {
            $order->tracking_status = $data['current_status'];
        }

        $order->tracking_last_update = now();
        $order->tracking_raw_json = $rawJson ?? json_encode($data);

        $this->applyOrderStatus($order, $status, $deliveredDate);
        $order->save();

        return $order;
    }

    public function applyOrderStatus(Order $order, ?string $status, mixed $deliveredDate = null): void
    {
        $normalized = strtolower(trim((string) $status));

        if ($this->isDeliveredStatus($normalized, $deliveredDate)) {
            $order->order_status = 'delivered';
            $order->delivered_at = $order->delivered_at ?? now();

            return;
        }

        if (in_array($order->order_status, ['pending', 'confirmed', 'packed'], true)) {
            $order->order_status = 'shipped';
        }
    }

    private function isDeliveredStatus(string $normalized, mixed $deliveredDate = null): bool
    {
        if ($normalized !== ''
            && str_contains($normalized, 'delivered')
            && ! str_contains($normalized, 'undelivered')
        ) {
            return true;
        }

        if (empty($deliveredDate) || $deliveredDate === '0000-00-00' || $deliveredDate === '0000-00-00 00:00:00') {
            return false;
        }

        return true;
    }
}
