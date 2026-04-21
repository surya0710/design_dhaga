<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ShiprocketWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        // Optional: log for debugging
        Log::info('Shiprocket Webhook', $data);

        $awb = $data['awb'] ?? null;

        if (!$awb) {
            return response()->json(['status' => 'ignored']);
        }

        $order = Order::where('awb_code', $awb)->first();

        if (!$order) {
            return response()->json(['status' => 'order_not_found']);
        }

        // Update tracking fields
        $order->tracking_status = $data['current_status'] ?? null;
        $order->tracking_last_update = now();
        $order->tracking_raw_json = json_encode($data);

        // Auto mark delivered
        if (($data['current_status'] ?? '') === 'Delivered') {
            $order->order_status = 'delivered';
            $order->delivered_at = now();
        }

        $order->save();

        return response()->json(['status' => 'success']);
    }
}
