<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ShipmentTrackingSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShiprocketWebhookController extends Controller
{
    public function handle(Request $request, ShipmentTrackingSyncService $sync)
    {
        try {
            if ($request->header('x-api-key') !== 'my-secret-123') {
                return response()->json(['status' => 'unauthorized'], 200);
            }

            Log::info('Shiprocket Webhook Raw', [
                'body' => $request->getContent(),
            ]);

            $data = $request->all();
            $awb = $data['awb'] ?? $data['awb_code'] ?? null;

            if (! $awb) {
                return response()->json([
                    'status' => 'ignored_no_awb',
                ], 200);
            }

            $order = Order::where('awb_code', $awb)->first();

            if (! $order) {
                return response()->json([
                    'status' => 'order_not_found',
                ], 200);
            }

            $sync->applyWebhookPayload($order, $data, $request->getContent());

            return response()->json([
                'status' => 'success',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Shiprocket Webhook Error', [
                'error' => $e->getMessage(),
                'payload' => $request->getContent(),
            ]);

            return response()->json([
                'status' => 'error_handled',
            ], 200);
        }
    }
}
