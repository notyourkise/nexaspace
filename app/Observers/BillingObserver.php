<?php

namespace App\Observers;

use App\Models\Billing;
use App\Services\BillingService;

class BillingObserver
{
    public function __construct(
        private readonly BillingService $billingService,
    ) {}

    public function updated(Billing $billing): void
    {
        if ($billing->wasChanged('status') && $billing->status === 'paid') {
            $this->billingService->restoreDevicesForBilling($billing);
        }
    }
}
