<?php

namespace App\Services;

use App\Mail\BillingCreatedMail;
use App\Mail\BillingThrottledMail;
use App\Models\Billing;
use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class BillingService
{
    public function __construct(
        private readonly MikroTikService $mikrotik, // used as fallback / for restoreDevicesForBilling
    ) {}

    /**
     * Find all unpaid billings past their grace period (due_date + 2 days)
     * and throttle the billing record, all active devices in the DB,
     * and push rate-limit to MikroTik.
     *
     * Grace rule: throttle triggers only when today > due_date + 2 days (H+3 or later).
     * Uses chunk(50) to avoid loading all overdue billings into memory at once.
     */
    public function checkAndThrottleOverdue(): void
    {
        // due_date < today - 2 days means the 2-day grace has fully expired.
        $graceCutoff = Carbon::today()->subDays(2);

        // Collect throttled tenants grouped by juragan_id for bulk notification.
        $throttledByJuragan = [];

        Billing::query()
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', $graceCutoff)
            ->with('user.devices', 'user.juragan')
            ->chunk(50, function ($billings) use (&$throttledByJuragan): void {
                foreach ($billings as $billing) {
                    $billing->update(['status' => 'throttled']);

                    $juragan        = $billing->user->juragan;
                    $juraganMikrotik = $juragan
                        ? MikroTikService::forJuragan($juragan)
                        : $this->mikrotik;

                    // Only touch active devices; leave 'blocked' devices untouched.
                    $billing->user
                        ->devices()
                        ->where('status', 'active')
                        ->get()
                        ->each(function (Device $device) use ($juraganMikrotik): void {
                            $device->update(['status' => 'throttled']);
                            $juraganMikrotik->throttleDevice($device->mac_address);
                            usleep(150_000); // 150ms jeda antar panggilan MikroTik
                        });

                    // Group for email: anak kos → their juragan
                    if ($juragan && $juragan->contact_email) {
                        $throttledByJuragan[$juragan->id] ??= ['juragan' => $juragan, 'tenants' => []];
                        $throttledByJuragan[$juragan->id]['tenants'][] = [
                            'name'        => $billing->user->name,
                            'room_number' => $billing->user->room_number,
                        ];
                    }
                }
            });

        // Send one throttle notification email per juragan.
        foreach ($throttledByJuragan as ['juragan' => $juragan, 'tenants' => $tenants]) {
            Mail::to($juragan->contact_email)->send(
                new BillingThrottledMail($juragan, $tenants)
            );
        }
    }

    /**
     * Generate monthly billing records for tenants WITHOUT a move_in_date.
     * Runs on the 1st of each month. Tenants with move_in_date are handled
     * by generateBillsForMoveInDay() which runs daily.
     * Idempotent: skips tenants that already have a bill this month.
     * Due date: 10th of the billing month.
     */
    public function generateMonthlyBills(): void
    {
        $billingMonth = Carbon::now()->startOfMonth();
        $dueDate      = Carbon::now()->startOfMonth()->addDays(9); // 10th of month

        $statsByJuragan = [];

        User::query()
            ->where('role', 'tenant')
            ->where('monthly_rate', '>', 0)
            ->whereNull('move_in_date') // Tenants with move_in_date have their own daily billing cycle
            ->with('juragan')
            ->chunk(50, function ($tenants) use ($billingMonth, $dueDate, &$statsByJuragan): void {
                foreach ($tenants as $tenant) {
                    $exists = Billing::query()
                        ->where('user_id', $tenant->id)
                        ->whereYear('billing_month', $billingMonth->year)
                        ->whereMonth('billing_month', $billingMonth->month)
                        ->exists();

                    if (! $exists) {
                        Billing::create([
                            'user_id'       => $tenant->id,
                            'amount'        => $tenant->monthly_rate,
                            'billing_month' => $billingMonth,
                            'due_date'      => $dueDate,
                            'status'        => 'unpaid',
                        ]);

                        $juragan = $tenant->juragan;
                        if ($juragan && $juragan->contact_email) {
                            $statsByJuragan[$juragan->id] ??= [
                                'juragan'     => $juragan,
                                'billCount'   => 0,
                                'totalAmount' => 0,
                            ];
                            $statsByJuragan[$juragan->id]['billCount']++;
                            $statsByJuragan[$juragan->id]['totalAmount'] += $tenant->monthly_rate;
                        }
                    }
                }
            });

        $monthLabel = $billingMonth->translatedFormat('F Y');

        foreach ($statsByJuragan as $stat) {
            Mail::to($stat['juragan']->contact_email)->send(
                new BillingCreatedMail(
                    $stat['juragan'],
                    $stat['billCount'],
                    $monthLabel,
                    $stat['totalAmount'],
                )
            );
        }
    }

    /**
     * Generate billing records for tenants whose move_in_date day matches the given day.
     * Called daily by GenerateBillsByMoveInJob. Idempotent per month.
     * Due date: 7 days after the billing day (generous grace for tenants on custom cycles).
     */
    public function generateBillsForMoveInDay(int $day): void
    {
        $today        = Carbon::today();
        $billingMonth = $today->copy()->startOfMonth();
        $dueDate      = $today->copy()->addDays(7);

        $statsByJuragan = [];

        User::query()
            ->where('role', 'tenant')
            ->where('monthly_rate', '>', 0)
            ->whereNotNull('move_in_date')
            ->whereRaw('DAY(move_in_date) = ?', [$day])
            ->with('juragan')
            ->chunk(50, function ($tenants) use ($billingMonth, $dueDate, &$statsByJuragan): void {
                foreach ($tenants as $tenant) {
                    $exists = Billing::query()
                        ->where('user_id', $tenant->id)
                        ->whereYear('billing_month', $billingMonth->year)
                        ->whereMonth('billing_month', $billingMonth->month)
                        ->exists();

                    if (! $exists) {
                        Billing::create([
                            'user_id'       => $tenant->id,
                            'amount'        => $tenant->monthly_rate,
                            'billing_month' => $billingMonth,
                            'due_date'      => $dueDate,
                            'status'        => 'unpaid',
                        ]);

                        $juragan = $tenant->juragan;
                        if ($juragan && $juragan->contact_email) {
                            $statsByJuragan[$juragan->id] ??= [
                                'juragan'     => $juragan,
                                'billCount'   => 0,
                                'totalAmount' => 0,
                            ];
                            $statsByJuragan[$juragan->id]['billCount']++;
                            $statsByJuragan[$juragan->id]['totalAmount'] += $tenant->monthly_rate;
                        }
                    }
                }
            });

        $monthLabel = $billingMonth->translatedFormat('F Y');

        foreach ($statsByJuragan as $stat) {
            Mail::to($stat['juragan']->contact_email)->send(
                new BillingCreatedMail(
                    $stat['juragan'],
                    $stat['billCount'],
                    $monthLabel,
                    $stat['totalAmount'],
                )
            );
        }
    }

    /**
     * Create a bill for a single tenant for the current month.
     * Returns true if a new bill was created, false if it already existed.
     * Due date: 7 days from today.
     */
    public function createBillForTenant(User $tenant): bool
    {
        $billingMonth = Carbon::today()->startOfMonth();
        $dueDate      = Carbon::today()->addDays(7);

        $exists = Billing::query()
            ->where('user_id', $tenant->id)
            ->whereYear('billing_month', $billingMonth->year)
            ->whereMonth('billing_month', $billingMonth->month)
            ->exists();

        if ($exists) {
            return false;
        }

        Billing::create([
            'user_id'       => $tenant->id,
            'amount'        => $tenant->monthly_rate,
            'billing_month' => $billingMonth,
            'due_date'      => $dueDate,
            'status'        => 'unpaid',
        ]);

        return true;
    }

    /**
     * Restore throttled devices to active in the DB and on the router
     * after a billing is marked paid.
     *
     * Devices that were manually 'blocked' by an admin are intentionally left alone.
     */
    public function restoreDevicesForBilling(Billing $billing): void
    {
        $billing->load('user.juragan', 'user.devices');

        $juragan        = $billing->user->juragan;
        $juraganMikrotik = $juragan
            ? MikroTikService::forJuragan($juragan)
            : $this->mikrotik;

        $billing->user
            ->devices()
            ->where('status', 'throttled')
            ->get()
            ->each(function (Device $device) use ($juraganMikrotik): void {
                $device->update(['status' => 'active']);
                $juraganMikrotik->unthrottleDevice($device->mac_address);
            });
    }
}
