<?php

namespace App\Providers;

use App\Models\Billing;
use App\Models\Subscription;
use App\Observers\BillingObserver;
use App\Observers\SubscriptionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Billing::observe(BillingObserver::class);
        Subscription::observe(SubscriptionObserver::class);
    }
}
