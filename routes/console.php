<?php

use App\Jobs\ThrottleOverdueTenantsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(ThrottleOverdueTenantsJob::class)->dailyAt('01:00');
