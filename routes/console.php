<?php

use App\Jobs\GenerateMonthlyBillsJob;
use App\Jobs\GenerateMonthlySubscriptionsJob;
use App\Jobs\SendBillingReminderJob;
use App\Jobs\SendSubscriptionReminderJob;
use App\Jobs\SuspendOverdueJuraganJob;
use App\Jobs\ThrottleOverdueTenantsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Throttle overdue tenants every day at 01:00 WITA
Schedule::job(ThrottleOverdueTenantsJob::class)
    ->dailyAt('01:00')
    ->name('throttle-overdue-tenants')
    ->withoutOverlapping();

// Generate monthly bills on the 1st of every month at 00:01 WITA
Schedule::job(GenerateMonthlyBillsJob::class)
    ->monthlyOn(1, '00:01')
    ->name('generate-monthly-bills')
    ->withoutOverlapping();

// Generate monthly subscription invoices for juragan on the 1st at 00:05 WITA
Schedule::job(GenerateMonthlySubscriptionsJob::class)
    ->monthlyOn(1, '00:05')
    ->name('generate-monthly-subscriptions')
    ->withoutOverlapping();

// Suspend juragan who are overdue on their subscription (daily 01:30 WITA)
Schedule::job(SuspendOverdueJuraganJob::class)
    ->dailyAt('01:30')
    ->name('suspend-overdue-juragan')
    ->withoutOverlapping();

// Send H-3 billing reminder to juragan (daily 08:00 WITA)
Schedule::job(SendBillingReminderJob::class)
    ->dailyAt('08:00')
    ->name('send-billing-reminder')
    ->withoutOverlapping();

// Send H-3 subscription reminder to juragan (daily 08:05 WITA)
Schedule::job(SendSubscriptionReminderJob::class)
    ->dailyAt('08:05')
    ->name('send-subscription-reminder')
    ->withoutOverlapping();
