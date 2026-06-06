<?php

namespace App\Jobs;

use App\Models\Billing;
use App\Services\BillingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RestoreDevicesJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /** Retry once more if the router is temporarily unreachable. */
    public int $tries = 3;

    /** Wait 30 seconds before each retry. */
    public int $backoff = 30;

    public function __construct(
        public readonly Billing $billing,
    ) {}

    public function handle(BillingService $billingService): void
    {
        $billingService->restoreDevicesForBilling($this->billing);
    }
}
