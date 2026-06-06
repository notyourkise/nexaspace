<?php

namespace App\Filament\Resources\BillingResource\Pages;

use App\Filament\Resources\BillingResource;
use App\Models\Billing;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateBilling extends CreateRecord
{
    protected static string $resource = BillingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && $user->isJuragan()) {
            $owns = User::where('id', $data['user_id'] ?? null)
                ->where('juragan_id', $user->id)
                ->exists();

            abort_unless($owns, 403);
        }

        // Persist move_in_date to the tenant user record, then strip from billing data.
        if (isset($data['user_id']) && array_key_exists('move_in_date', $data)) {
            User::where('id', $data['user_id'])->update(['move_in_date' => $data['move_in_date']]);
        }
        unset($data['move_in_date']);

        return $data;
    }

    /**
     * After the current bill is created, backfill any months between
     * (move_in_date + 1 month) and the current month that have no bill yet.
     *
     * Rule: billing starts the month AFTER move_in_date.
     * E.g., move_in = Jan → first bill = Feb → backfills Feb…May when today is June.
     *
     * Amount used for backfill = same as the bill just created (what the juragan entered).
     */
    protected function afterCreate(): void
    {
        $billing = $this->record;
        $tenant  = User::find($billing->user_id);

        if (! $tenant || ! $tenant->move_in_date) {
            return;
        }

        $billingMonth = $billing->billing_month; // Carbon, cast by Billing model
        if (! $billingMonth) {
            return;
        }

        // First billing month = month AFTER the move-in month
        $firstBillingMonth = $tenant->move_in_date->copy()->startOfMonth()->addMonth();
        $currentMonth      = Carbon::now()->startOfMonth();

        // Nothing to backfill if the first billing month is in the future
        if ($firstBillingMonth->greaterThan($currentMonth)) {
            return;
        }

        $amount  = $billing->amount; // use the amount the juragan just confirmed
        $created = 0;
        $month   = $firstBillingMonth->copy();

        while ($month->lessThanOrEqualTo($currentMonth)) {
            // Skip the bill that was just created by the form submission
            if ($month->isSameMonth($billingMonth)) {
                $month->addMonth();
                continue;
            }

            $exists = Billing::query()
                ->where('user_id', $tenant->id)
                ->whereYear('billing_month', $month->year)
                ->whereMonth('billing_month', $month->month)
                ->exists();

            if (! $exists) {
                $day     = $tenant->move_in_date->day;
                $maxDay  = $month->daysInMonth;
                $dueDate = $month->copy()->setDay(min($day, $maxDay))->addDays(7);

                Billing::create([
                    'user_id'       => $tenant->id,
                    'amount'        => $amount,
                    'billing_month' => $month->copy()->startOfMonth(),
                    'due_date'      => $dueDate,
                    'status'        => 'unpaid',
                ]);

                $created++;
            }

            $month->addMonth();
        }

        if ($created > 0) {
            $label = $firstBillingMonth->translatedFormat('F Y');

            Notification::make()
                ->title("{$created} tagihan bulan sebelumnya otomatis dibuat")
                ->body("Backfill dari {$label} — semua bulan yang belum ada tagihan sudah ditambahkan.")
                ->success()
                ->send();
        }
    }
}
