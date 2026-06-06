<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class GenerateMonthlySubscriptionsJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 2;
    public int $backoff = 60;

    public function handle(): void
    {
        $subscriptionMonth = Carbon::now()->startOfMonth();
        $dueDate           = Carbon::now()->startOfMonth()->addDays(9); // 10th

        User::query()
            ->where('role', 'juragan')
            ->whereIn('plan', ['lite', 'pro'])
            ->chunk(50, function ($juraganList) use ($subscriptionMonth, $dueDate): void {
                foreach ($juraganList as $juragan) {
                    $exists = Subscription::query()
                        ->where('juragan_id', $juragan->id)
                        ->whereYear('subscription_month', $subscriptionMonth->year)
                        ->whereMonth('subscription_month', $subscriptionMonth->month)
                        ->exists();

                    if (! $exists) {
                        Subscription::create([
                            'juragan_id'         => $juragan->id,
                            'amount'             => $this->amountForPlan($juragan->plan),
                            'subscription_month' => $subscriptionMonth,
                            'due_date'           => $dueDate,
                            'status'             => 'unpaid',
                        ]);
                    }
                }
            });
    }

    private function amountForPlan(string $plan): int
    {
        return match ($plan) {
            'lite' => 199_000,
            'pro'  => 499_000,
            default => 0,
        };
    }
}
