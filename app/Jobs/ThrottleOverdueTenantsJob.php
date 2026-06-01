<?php

namespace App\Jobs;

use App\Services\BillingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ThrottleOverdueTenantsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /** Retry once more if the router is temporarily unreachable. */
    public int $tries = 2;

    /** Wait 60 seconds before the retry. */
    public int $backoff = 60;

    public function handle(BillingService $billingService): void
    {
        $billingService->checkAndThrottleOverdue();
    }
}
