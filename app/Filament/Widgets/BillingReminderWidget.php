<?php

namespace App\Filament\Widgets;

use App\Models\Billing;
use App\Models\User;
use App\Services\BillingService;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BillingReminderWidget extends Widget
{
    protected string $view = 'filament.widgets.billing-reminder';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->isJuragan() ?? false;
    }

    protected function getViewData(): array
    {
        $juragan      = auth()->user();
        $today        = Carbon::today();
        $billingMonth = $today->copy()->startOfMonth();

        $tenants = User::query()
            ->where('juragan_id', $juragan->id)
            ->where('role', 'tenant')
            ->whereNotNull('move_in_date')
            ->whereRaw('DAY(move_in_date) = ?', [$today->day])
            ->where('monthly_rate', '>', 0)
            ->orderBy('room_number')
            ->get()
            ->filter(fn (User $t) => ! Billing::query()
                ->where('user_id', $t->id)
                ->whereYear('billing_month', $billingMonth->year)
                ->whereMonth('billing_month', $billingMonth->month)
                ->exists()
            )
            ->values();

        // Also fetch tenants whose billing day is in the next 3 days (preview)
        $upcoming = User::query()
            ->where('juragan_id', $juragan->id)
            ->where('role', 'tenant')
            ->whereNotNull('move_in_date')
            ->where('monthly_rate', '>', 0)
            ->orderBy('room_number')
            ->get()
            ->filter(function (User $t) use ($today, $billingMonth): bool {
                $day = $t->move_in_date->day;
                $diff = $day - $today->day;
                // Upcoming: 1-3 days from now, hasn't been billed this month
                if ($diff < 1 || $diff > 3) return false;

                return ! Billing::query()
                    ->where('user_id', $t->id)
                    ->whereYear('billing_month', $billingMonth->year)
                    ->whereMonth('billing_month', $billingMonth->month)
                    ->exists();
            })
            ->values();

        return [
            'tenants'  => $tenants,
            'upcoming' => $upcoming,
            'today'    => $today,
        ];
    }

    /** Create a bill for a single tenant (called from Blade via Livewire). */
    public function createBillForTenant(int $tenantId): void
    {
        $juragan = auth()->user();

        $tenant = User::where('id', $tenantId)
            ->where('juragan_id', $juragan->id)
            ->where('role', 'tenant')
            ->firstOrFail();

        $created = app(BillingService::class)->createBillForTenant($tenant);

        if ($created) {
            Notification::make()
                ->title("Tagihan Kamar {$tenant->room_number} ({$tenant->name}) berhasil dibuat")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title("Tagihan bulan ini sudah ada untuk {$tenant->name}")
                ->warning()
                ->send();
        }
    }

    /** Create bills for all tenants due today (called from Blade via Livewire). */
    public function createAllBills(): void
    {
        $juragan      = auth()->user();
        $today        = Carbon::today();
        $billingMonth = $today->copy()->startOfMonth();

        $tenants = User::query()
            ->where('juragan_id', $juragan->id)
            ->where('role', 'tenant')
            ->whereNotNull('move_in_date')
            ->whereRaw('DAY(move_in_date) = ?', [$today->day])
            ->where('monthly_rate', '>', 0)
            ->get();

        $service = app(BillingService::class);
        $created = $tenants->filter(fn ($t) => $service->createBillForTenant($t))->count();

        if ($created > 0) {
            Notification::make()
                ->title("{$created} tagihan berhasil dibuat untuk hari ini")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Semua tagihan sudah ada — tidak ada yang perlu dibuat')
                ->warning()
                ->send();
        }
    }
}
