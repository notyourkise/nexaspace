<?php

use App\Http\Controllers\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

// Public webhook endpoint — CSRF exempt (configured in bootstrap/app.php)
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handle'])
    ->name('webhook.midtrans');
