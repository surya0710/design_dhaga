<?php

namespace App\Http\Controllers;

use App\Services\ShiprocketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShiprocketController extends Controller
{
    public function check(Request $request, ShiprocketService $shiprocket): JsonResponse
    {
        $validated = $request->validate([
            'delivery_postcode' => ['required', 'digits:6'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'cod' => ['nullable', 'boolean'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'breadth' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'declared_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $result = $shiprocket->checkServiceability(
                pickupPincode: '125001',
                deliveryPincode: $validated['delivery_postcode'],
                weight: (float) $validated['weight'],
                cod: (int) ($validated['cod'] ?? 0),
                length: isset($validated['length']) ? (float) $validated['length'] : null,
                breadth: isset($validated['breadth']) ? (float) $validated['breadth'] : null,
                height: isset($validated['height']) ? (float) $validated['height'] : null,
                declaredValue: isset($validated['declared_value']) ? (float) $validated['declared_value'] : null,
            );

            return response()->json([
                'success' => true,
                'message' => $result['serviceable']
                    ? 'Pincode is serviceable.'
                    : 'Pincode is not serviceable.',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to check serviceability.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}