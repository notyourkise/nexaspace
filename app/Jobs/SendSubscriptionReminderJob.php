<?php

namespace App\Jobs;

use App\Mail\SubscriptionReminderMail;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionReminderJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public int $tries  = 3;
    public int $backoff = 60;

    /**
     * Find all unpaid subscriptions whose due_date is exactly 3 days from today
     * and send a reminder email to each juragan who has a contact_email.
     */
    public function handle(): void
    {
        $reminderDate = Carbon::today()->addDays(3);

        Subscription::query()
            ->where('status', 'unpaid')
            ->whereDate('due_date', $reminderDate)
            ->with('juragan')
            ->chunk(50, function ($subscriptions): void {
                foreach ($subscriptions as $subscription) {
                    $juragan = $subscription->juragan;

                    if (! $juragan || ! $juragan->contact_email) {
                        continue;
                    }

                    $dueDate = Carbon::parse($subscription->due_date)->translatedFormat('d F Y');

                    Mail::to($juragan->contact_email)->send(
                        new SubscriptionReminderMail($juragan, $subscription, $dueDate)
                    );
                }
            });
    }
}
