<?php

namespace App\Services;

use App\Models\Billing;
use Midtrans\Config;
use Midtrans\Snap;
use Throwable;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Generate a Midtrans Snap token for a given Billing record.
     * Returns the token string, or null if the API call fails.
     */
    public function getSnapToken(Billing $billing): ?string
    {
        $params = [
            'transaction_details' => [
                'order_id'     => 'BILL-' . $billing->id . '-' . time(),
                'gross_amount' => (int) $billing->amount,
            ],
            'customer_details' => [
                'first_name' => $billing->user->name,
                'email'      => $billing->user->email,
                'phone'      => $billing->user->phone_number ?? '',
            ],
            'item_details' => [
                [
                    'id'       => 'BILLING-' . $billing->id,
                    'price'    => (int) $billing->amount,
                    'quantity' => 1,
                    'name'     => 'WiFi Bill ' . $billing->billing_month->format('F Y'),
                ],
            ],
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (Throwable $e) {
            Log::error('[Midtrans] Failed to generate snap token', [
                'billing_id' => $billing->id,
                'error'      => $e->getMessage(),
            ]);

            return null;
        }
    }
}
