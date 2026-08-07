<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncShipmentTracking;
use App\Models\Order;
use App\Services\ShiprocketService;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ✅ Your tracking sync job
Schedule::job(new SyncShipmentTracking)->everyThirtyMinutes();
Schedule::command('generate:sitemap')->daily();
Schedule::command('instagram:auto-refresh')->daily();

// Manual test command to debug Shiprocket tracking for a single AWB
Artisan::command('tracking:test {awb}', function (string $awb) {
    $this->info("Testing tracking for AWB: {$awb}");

    $order = Order::where('awb_code', $awb)->first();

    if (! $order) {
        $this->error('Order not found for this AWB.');
        return self::FAILURE;
    }

    $this->line('Current DB status:');
    $this->line(' - order_status: ' . ($order->order_status ?? 'null'));
    $this->line(' - tracking_status: ' . ($order->tracking_status ?? 'null'));

    try {
        /** @var ShiprocketService $shiprocket */
        $shiprocket = app(ShiprocketService::class);

        $tracking = $shiprocket->trackOrder($awb);
        $status   = $tracking['current_status'] ?? null;

        $this->line('Shiprocket current_status: ' . ($status ?? 'null'));

        $order->tracking_status      = $status;
        $order->tracking_last_update = $tracking['delivered_date'] ?? now();
        $order->tracking_raw_json    = json_encode($tracking['raw'] ?? $tracking);

        if ($status && str_contains(strtolower($status), 'delivered')) {
            if (! $order->delivered_at) {
                $order->delivered_at = now();
            }
            $order->order_status = 'delivered';
        }

        $order->save();

        $this->info('Order updated. New DB status:');
        $this->line(' - order_status: ' . ($order->order_status ?? 'null'));
        $this->line(' - tracking_status: ' . ($order->tracking_status ?? 'null'));

        return self::SUCCESS;
    } catch (\Throwable $e) {
        $this->error('Error while calling Shiprocket: ' . $e->getMessage());

        Log::error('tracking:test failed', [
            'awb'   => $awb,
            'order' => $order->id ?? null,
            'error' => $e->getMessage(),
        ]);

        return self::FAILURE;
    }
})->describe('Test Shiprocket tracking and DB update for a single AWB.');