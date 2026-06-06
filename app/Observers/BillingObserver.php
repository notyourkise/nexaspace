<?php

namespace App\Observers;

use App\Jobs\RestoreDevicesJob;
use App\Models\ActivityLog;
use App\Models\Billing;

class BillingObserver
{
    public function updated(Billing $billing): void
    {
        if (! $billing->wasChanged('status')) {
            return;
        }

        $old = $billing->getOriginal('status');
        $new = $billing->status;

        ActivityLog::record(
            event: 'billing.status_changed',
            description: "Tagihan #{$billing->id} ({$billing->user?->name}) diubah dari {$old} → {$new}.",
            subject: $billing,
            properties: ['old_status' => $old, 'new_status' => $new, 'amount' => $billing->amount],
        );

        if ($new === 'paid') {
            RestoreDevicesJob::dispatch($billing);
        }
    }
}
