<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/daftar/sukses', fn () => view('daftar-sukses'))->name('daftar.sukses');

Route::get('/daftar/pembayaran/{registration}', [RegistrationController::class, 'payment'])
    ->middleware('signed')
    ->name('daftar.payment');

Route::get('/daftar/{plan?}', [RegistrationController::class, 'show'])
    ->where('plan', 'lite|pro|custom')
    ->name('daftar');

Route::post('/register', [RegistrationController::class, 'store'])->name('registration.store');

// CSV exports — auth enforced inside the controller; no login redirect needed
Route::get('/export/billing', [ExportController::class, 'billing'])->name('export.billing');
Route::get('/export/subscription', [ExportController::class, 'subscription'])->name('export.subscription');

// PDF invoice download — auth enforced inside the controller
Route::get('/invoice/billing/{billing}', [InvoiceController::class, 'download'])->name('invoice.billing');

// Health check — can be pinged by uptime monitors (e.g. UptimeRobot)
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $db = 'ok';
    } catch (\Throwable) {
        $db = 'error';
    }

    $failedJobs = DB::table('failed_jobs')->count();

    return response()->json([
        'status'      => $db === 'ok' ? 'ok' : 'degraded',
        'timestamp'   => now()->toISOString(),
        'timezone'    => config('app.timezone'),
        'environment' => config('app.env'),
        'checks'      => [
            'database'    => $db,
            'failed_jobs' => $failedJobs,
        ],
    ], $db === 'ok' ? 200 : 503);
})->name('health');

