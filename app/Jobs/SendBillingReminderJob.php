<?php

namespace App\Jobs;

use App\Mail\BillingReminderMail;
use App\Models\Billing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendBillingReminderJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public int $tries  = 3;
    public int $backoff = 60;

    /**
     * Find all unpaid billings whose due_date is exactly 3 days from today
     * and send one reminder email per juragan listing their unpaid tenants.
     */
    public function handle(): void
    {
        $reminderDate = Carbon::today()->addDays(3);

        // Group by juragan: only send to juragan who have a contact_email.
        $byJuragan = [];

        Billing::query()
            ->where('status', 'unpaid')
            ->whereDate('due_date', $reminderDate)
            ->with('user.juragan')
            ->chunk(50, function ($billings) use (&$byJuragan): void {
                foreach ($billings as $billing) {
                    $juragan = $billing->user?->juragan;

                    if (! $juragan || ! $juragan->contact_email) {
                        continue;
                    }

                    $byJuragan[$juragan->id] ??= [
                        'juragan'  => $juragan,
                        'dueDate'  => Carbon::parse($billing->due_date)->translatedFormat('d F Y'),
                        'tenants'  => [],
                    ];

                    $byJuragan[$juragan->id]['tenants'][] = [
                        'name'        => $billing->user->name,
                        'room_number' => $billing->user->room_number,
                        'amount'      => $billing->amount,
                    ];
                }
            });

        foreach ($byJuragan as ['juragan' => $juragan, 'dueDate' => $dueDate, 'tenants' => $tenants]) {
            Mail::to($juragan->contact_email)->send(
                new BillingReminderMail($juragan, $tenants, $dueDate)
            );
        }
    }
}
