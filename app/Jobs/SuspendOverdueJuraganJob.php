<?php

namespace App\Jobs;

use App\Mail\JuraganSuspendedMail;
use App\Models\ActivityLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SuspendOverdueJuraganJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 2;
    public int $backoff = 60;

    public function handle(): void
    {
        // Grace period: suspend only when today > due_date + 2 days.
        $graceCutoff = Carbon::today()->subDays(2);

        Subscription::query()
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', $graceCutoff)
            ->with('juragan')
            ->chunk(50, function ($subscriptions): void {
                foreach ($subscriptions as $subscription) {
                    $subscription->update(['status' => 'overdue']);

                    if ($subscription->juragan && $subscription->juragan->suspended_at === null) {
                        $subscription->juragan->update(['suspended_at' => now()]);

                        ActivityLog::record(
                            event: 'juragan.suspended',
                            description: "Akun juragan {$subscription->juragan->kos_name} disuspend karena langganan #{$subscription->id} menunggak.",
                            subject: $subscription->juragan,
                            properties: ['subscription_id' => $subscription->id, 'due_date' => $subscription->due_date],
                        );

                        if ($subscription->juragan->contact_email) {
                            Mail::to($subscription->juragan->contact_email)->send(
                                new JuraganSuspendedMail($subscription->juragan, $subscription)
                            );
                        }
                    }
                }
            });
    }
}
