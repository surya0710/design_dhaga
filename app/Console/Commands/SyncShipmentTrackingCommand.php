<?php

namespace App\Console\Commands;

use App\Services\ShipmentTrackingSyncService;
use Illuminate\Console\Command;

class SyncShipmentTrackingCommand extends Command
{
    protected $signature = 'shipments:sync-tracking';

    protected $description = 'Poll Shiprocket and update open order statuses';

    public function handle(ShipmentTrackingSyncService $sync): int
    {
        $count = $sync->syncOpenOrders();

        $this->info("Synced tracking for {$count} open order(s).");

        return self::SUCCESS;
    }
}
