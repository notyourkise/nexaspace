<?php

namespace App\Observers;

use App\Jobs\RestoreDevicesJob;
use App\Models\Billing;

class BillingObserver
{
    public function updated(Billing $billing): void
    {
        if ($billing->wasChanged('status') && $billing->status === 'paid') {
            RestoreDevicesJob::dispatch($billing);
        }
    }
}
