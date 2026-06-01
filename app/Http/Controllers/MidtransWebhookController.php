<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        try {
            $payload = $request->all();

            // Verify signature to ensure the notification is genuinely from Midtrans.
            // Formula: SHA512(order_id + status_code + gross_amount + server_key)
            $serverKey     = config('services.midtrans.server_key');
            $orderId       = $payload['order_id']       ?? '';
            $statusCode    = $payload['status_code']    ?? '';
            $grossAmount   = $payload['gross_amount']   ?? '';
            $expectedSig   = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            $receivedSig   = $payload['signature_key'] ?? '';

            if (! hash_equals($expectedSig, $receivedSig)) {
                Log::warning('[Midtrans] Invalid signature', ['order_id' => $orderId]);

                return response('Forbidden', 403);
            }

            $transactionStatus = $payload['transaction_status'] ?? '';
            $fraudStatus       = $payload['fraud_status']       ?? 'accept';

            // Only mark paid on settlement (transfer/VA) or capture + accept (card).
            $isPaid = $transactionStatus === 'settlement'
                || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

            if (! $isPaid) {
                return response('OK', 200);
            }

            // Parse billing ID from order_id pattern "BILL-{id}-{timestamp}"
            $parts     = explode('-', $orderId);
            $billingId = $parts[1] ?? null;

            if (! $billingId) {
                Log::warning('[Midtrans] Could not parse billing ID from order_id', ['order_id' => $orderId]);

                return response('OK', 200);
            }

            $billing = Billing::find((int) $billingId);

            if (! $billing) {
                Log::warning('[Midtrans] Billing not found', ['billing_id' => $billingId]);

                return response('OK', 200);
            }

            // Updating status to 'paid' triggers BillingObserver which unthrottles devices.
            if ($billing->status !== 'paid') {
                $billing->update(['status' => 'paid']);

                Log::info('[Midtrans] Billing marked paid via webhook', [
                    'billing_id' => $billing->id,
                    'order_id'   => $orderId,
                ]);
            }

            return response('OK', 200);
        } catch (Throwable $e) {
            Log::error('[Midtrans] Webhook error', ['error' => $e->getMessage()]);

            return response('Internal Server Error', 500);
        }
    }
}
