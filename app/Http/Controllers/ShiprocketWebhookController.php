<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ShiprocketWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            // Log raw payload (important for debugging)
            Log::info('Shiprocket Webhook Raw', [
                'body' => $request->getContent()
            ]);

            $data = $request->all();

            $awb = $data['awb'] ?? null;

            if (!$awb) {
                return response()->json([
                    'status' => 'ignored_no_awb'
                ], 200);
            }

            $order = Order::where('awb_code', $awb)->first();

            if (!$order) {
                return response()->json([
                    'status' => 'order_not_found'
                ], 200);
            }

            // Update tracking safely
            if (isset($data['current_status'])) {
                $order->tracking_status = $data['current_status'];
            }

            $order->tracking_last_update = now();
            $order->tracking_raw_json = $request->getContent();

            // Mark delivered (case-insensitive)
            if (strtolower($data['current_status'] ?? '') === 'delivered') {
                $order->order_status = 'delivered';
                $order->delivered_at = now();
            }

            $order->save();

            return response()->json([
                'status' => 'success'
            ], 200);

        } catch (\Exception $e) {

            Log::error('Shiprocket Webhook Error', [
                'error' => $e->getMessage(),
                'payload' => $request->getContent()
            ]);

            // IMPORTANT: Always return 200
            return response()->json([
                'status' => 'error_handled'
            ], 200);
        }
    }
}