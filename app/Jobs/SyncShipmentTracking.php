<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Order;
use App\Services\ShiprocketService;
use Illuminate\Support\Facades\Log;

class SyncShipmentTracking implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $orders = Order::whereNotNull('awb_code')
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

                $tracking = app(ShiprocketService::class)
                    ->trackOrder($order->awb_code);

                if (!empty($tracking)) {
                    $status = $tracking['current_status'] ?? null;

                    Log::info('SyncShipmentTracking response', [
                        'order_id'       => $order->id,
                        'awb_code'       => $order->awb_code,
                        'current_status' => $status,
                    ]);

                    $order->tracking_status = $status;
                    $order->tracking_last_update = $tracking['delivered_date'] ?? now();
                    $order->tracking_raw_json = json_encode($tracking['raw'] ?? $tracking);

                    // Auto mark delivered (handle different capitalisations / phrasing)
                    if ($status && str_contains(strtolower($status), 'delivered')) {
                        if (! $order->delivered_at) {
                            $order->delivered_at = now();
                        }
                        $order->order_status = 'delivered';
                    }

                    $order->save();
                }
            } catch (\Throwable $e) {
                Log::error('Tracking failed', [
                    'order_id' => $order->id ?? null,
                    'error'    => $e->getMessage(),
                ]);
            }
        }
    }
}
