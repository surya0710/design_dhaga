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

        foreach ($orders as $order) {
            try {
                $tracking = app(ShiprocketService::class)
                    ->trackShipment($order->awb_code);

                if (!empty($tracking)) {

                    $shipment = $tracking['shipment_track'][0] ?? null;

                    $order->tracking_status = $shipment['current_status'] ?? null;
                    $order->tracking_last_update = $shipment['updated_date'] ?? now();
                    $order->tracking_raw_json = json_encode($tracking);

                    // Auto mark delivered
                    if (($shipment['current_status'] ?? '') === 'Delivered') {
                        $order->order_status = 'delivered';
                        $order->delivered_at = now();
                    }

                    $order->save();
                }

            } catch (\Throwable $e) {
                Log::error('Tracking failed', ['order_id' => $order->id]);
            }
        }
    }
}
