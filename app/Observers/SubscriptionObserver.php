<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Subscription;

class SubscriptionObserver
{
    public function updated(Subscription $subscription): void
    {
        if (! $subscription->wasChanged('status')) {
            return;
        }

        $old = $subscription->getOriginal('status');
        $new = $subscription->status;

        ActivityLog::record(
            event: 'subscription.status_changed',
            description: "Langganan #{$subscription->id} ({$subscription->juragan?->kos_name}) diubah dari {$old} → {$new}.",
            subject: $subscription,
            properties: ['old_status' => $old, 'new_status' => $new, 'amount' => $subscription->amount],
        );

        if ($new === 'paid') {
            $subscription->juragan->update(['suspended_at' => null]);

            ActivityLog::record(
                event: 'juragan.unsuspended',
                description: "Akses panel juragan {$subscription->juragan?->kos_name} dipulihkan setelah langganan lunas.",
                subject: $subscription->juragan,
            );
        }
    }
}
