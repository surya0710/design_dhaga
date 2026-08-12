<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_cart_value', 'max_discount',
        'start_date', 'end_date', 'is_single_use', 'status', 'free_shipping'
    ];

    /**
     * Re-validate and recalculate the session coupon against the current cart subtotal.
     * Removes the coupon from session when it is no longer valid (e.g. below min cart value).
     *
     * @return array{coupon: array|null, removed: bool, message: string|null}
     */
    public static function syncSessionForSubtotal(float $subtotal): array
    {
        $sessionCoupon = session('coupon');

        if (!$sessionCoupon || empty($sessionCoupon['code'])) {
            return [
                'coupon' => null,
                'removed' => false,
                'message' => null,
            ];
        }

        $coupon = static::where('code', $sessionCoupon['code'])->first();

        if (!$coupon || (int) $coupon->status !== 1) {
            session()->forget('coupon');
            return [
                'coupon' => null,
                'removed' => true,
                'message' => 'Coupon is no longer valid and has been removed.',
            ];
        }

        $now = now();

        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            session()->forget('coupon');
            return [
                'coupon' => null,
                'removed' => true,
                'message' => 'Coupon is not active yet and has been removed.',
            ];
        }

        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            session()->forget('coupon');
            return [
                'coupon' => null,
                'removed' => true,
                'message' => 'Coupon has expired and has been removed.',
            ];
        }

        $minCartValue = (float) $coupon->min_cart_value;
        if ($minCartValue > 0 && $subtotal < $minCartValue) {
            session()->forget('coupon');
            return [
                'coupon' => null,
                'removed' => true,
                'message' => 'Coupon removed: minimum cart value of ₹' . number_format($minCartValue, 2) . ' required.',
            ];
        }

        $freeShipping = (isset($coupon->free_shipping) && $coupon->free_shipping)
            || $coupon->type === 'shipping';

        $discount = 0.0;
        if ($coupon->type === 'fixed') {
            $discount = (float) $coupon->value;
        } elseif ($coupon->type === 'percent') {
            $discount = ($subtotal * (float) $coupon->value) / 100;
        }

        if ($coupon->max_discount && $discount > (float) $coupon->max_discount) {
            $discount = (float) $coupon->max_discount;
        }

        $discount = min($discount, $subtotal);

        $synced = [
            'code' => $coupon->code,
            'discount' => round($discount, 2),
            'free_shipping' => $freeShipping,
        ];

        session()->put('coupon', $synced);

        return [
            'coupon' => $synced,
            'removed' => false,
            'message' => null,
        ];
    }
}
