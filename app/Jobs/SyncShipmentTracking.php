<?php

namespace App\Jobs;

use App\Services\ShipmentTrackingSyncService;
use Illuminate\Foundation\Queue\Queueable;

class SyncShipmentTracking
{
    use Queueable;

    public function handle(ShipmentTrackingSyncService $sync): void
    {
        $sync->syncOpenOrders();
    }
}
