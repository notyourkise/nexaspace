<?php

namespace App\Observers;

use App\Models\Subscription;

class SubscriptionObserver
{
    public function updated(Subscription $subscription): void
    {
        if ($subscription->wasChanged('status') && $subscription->status === 'paid') {
            $subscription->juragan->update(['suspended_at' => null]);
        }
    }
}
