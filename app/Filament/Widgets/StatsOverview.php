<?php

namespace App\Filament\Widgets;

use App\Models\Billing;
use App\Models\Device;
use App\Models\Registration;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $user = auth()->user();

        return ($user && $user->isJuragan())
            ? $this->juraganStats($user)
            : $this->developerStats();
    }

    /** Platform-wide stats for the developer (super admin). */
    private function developerStats(): array
    {
        $totalRevenue = Billing::where('status', 'paid')
            ->whereYear('billing_month', Carbon::now()->year)
            ->whereMonth('billing_month', Carbon::now()->month)
            ->sum('amount');

        $failedJobs = DB::table('failed_jobs')->count();

        return [
            Stat::make('Total Juragan', User::where('role', 'juragan')->count())
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Total Anak Kos', User::where('role', 'tenant')->count())
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Active Devices', Device::where('status', 'active')->count())
                ->icon('heroicon-o-device-phone-mobile')
                ->color('success'),

            Stat::make('Unpaid Bills', Billing::where('status', 'unpaid')->count())
                ->icon('heroicon-o-exclamation-circle')
                ->color('warning'),

            Stat::make('Revenue This Month', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Pendaftaran Baru', Registration::where('status', 'pending')->count())
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),

            Stat::make('Failed Jobs', $failedJobs)
                ->icon('heroicon-o-x-circle')
                ->color($failedJobs > 0 ? 'danger' : 'success'),
        ];
    }

    /** Stats scoped to a single juragan's own anak kos. */
    private function juraganStats(User $juragan): array
    {
        $tenantIds = User::where('juragan_id', $juragan->id)
            ->where('role', 'tenant')
            ->pluck('id');

        $tenantCount  = $tenantIds->count();
        $quota        = (int) ($juragan->room_quota ?: 0);
        $quotaUsed    = $tenantCount;
        $quotaSisa    = max(0, $quota - $quotaUsed);

        $revenue = Billing::whereIn('user_id', $tenantIds)
            ->where('status', 'paid')
            ->whereYear('billing_month', Carbon::now()->year)
            ->whereMonth('billing_month', Carbon::now()->month)
            ->sum('amount');

        $unpaidCount    = Billing::whereIn('user_id', $tenantIds)->where('status', 'unpaid')->count();
        $throttledCount = Billing::whereIn('user_id', $tenantIds)->where('status', 'throttled')->count();

        $activeDevices    = Device::whereIn('user_id', $tenantIds)->where('status', 'active')->count();
        $throttledDevices = Device::whereIn('user_id', $tenantIds)->where('status', 'throttled')->count();

        // Quota display: "5 / 20 kamar (15 sisa)" or "Full" when quota exhausted
        $quotaLabel = $quota > 0
            ? "{$quotaUsed} / {$quota} kamar"
            : "{$quotaUsed} kamar";
        $quotaDesc = $quota > 0
            ? ($quotaSisa > 0 ? "{$quotaSisa} slot tersisa" : 'Kuota penuh')
            : 'Kuota tidak terdefinisi';
        $quotaColor = $quota > 0 && $quotaSisa === 0 ? 'danger' : 'primary';

        return [
            Stat::make('Kamar Terisi', $quotaLabel)
                ->description($quotaDesc)
                ->icon('heroicon-o-users')
                ->color($quotaColor),

            Stat::make('Perangkat Aktif', $activeDevices)
                ->description($throttledDevices > 0 ? "{$throttledDevices} perangkat di-throttle" : 'Semua normal')
                ->icon('heroicon-o-device-phone-mobile')
                ->color($throttledDevices > 0 ? 'warning' : 'success'),

            Stat::make('Tagihan Belum Lunas', $unpaidCount)
                ->description($throttledCount > 0 ? "{$throttledCount} sudah di-throttle" : 'Belum ada yang di-throttle')
                ->icon('heroicon-o-exclamation-circle')
                ->color($throttledCount > 0 ? 'danger' : ($unpaidCount > 0 ? 'warning' : 'success')),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($revenue, 0, ',', '.'))
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            $this->subscriptionStat($juragan),
        ];
    }

    /** Kartu status langganan NexaSpace untuk juragan bulan ini. */
    private function subscriptionStat(User $juragan): Stat
    {
        $now          = Carbon::now();
        $subscription = Subscription::query()
            ->where('juragan_id', $juragan->id)
            ->whereYear('subscription_month', $now->year)
            ->whereMonth('subscription_month', $now->month)
            ->first();

        if (! $subscription) {
            return Stat::make('Langganan NexaSpace', 'Belum Ada Tagihan')
                ->icon('heroicon-o-credit-card')
                ->color('gray');
        }

        [$label, $color] = match ($subscription->status) {
            'paid'   => ['Aktif — Lunas', 'success'],
            'unpaid' => ['Belum Lunas', 'warning'],
            'overdue' => ['Menunggak — Akses Diblokir', 'danger'],
            default  => [$subscription->status, 'gray'],
        };

        return Stat::make('Langganan NexaSpace', $label)
            ->description('Jatuh tempo: ' . $subscription->due_date->format('d M Y'))
            ->icon('heroicon-o-credit-card')
            ->color($color);
    }
}
