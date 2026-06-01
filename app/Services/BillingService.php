<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Device;
use Illuminate\Support\Carbon;

class BillingService
{
    public function __construct(
        private readonly MikroTikService $mikrotik,
    ) {}

    /**
     * Find all unpaid billings past their grace period (due_date + 2 days)
     * and throttle the billing record, all active devices in the DB,
     * and push rate-limit to MikroTik.
     *
     * Grace rule: throttle triggers only when today > due_date + 2 days (H+3 or later).
     */
    public function checkAndThrottleOverdue(): void
    {
        // due_date < today - 2 days means the 2-day grace has fully expired.
        $graceCutoff = Carbon::today()->subDays(2);

        Billing::query()
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', $graceCutoff)
            ->with('user.devices')
            ->get()
            ->each(function (Billing $billing): void {
                $billing->update(['status' => 'throttled']);

                // Only touch active devices; leave 'blocked' devices untouched.
                $billing->user
                    ->devices()
                    ->where('status', 'active')
                    ->get()
                    ->each(function (Device $device): void {
                        $device->update(['status' => 'throttled']);
                        $this->mikrotik->throttleDevice($device->mac_address);
                    });
            });
    }

    /**
     * Restore throttled devices to active in the DB and on the router
     * after a billing is marked paid.
     *
     * Devices that were manually 'blocked' by an admin are intentionally left alone.
     */
    public function restoreDevicesForBilling(Billing $billing): void
    {
        $billing->load('user.devices');

        $billing->user
            ->devices()
            ->where('status', 'throttled')
            ->get()
            ->each(function (Device $device): void {
                $device->update(['status' => 'active']);
                $this->mikrotik->unthrottleDevice($device->mac_address);
            });
    }
}
