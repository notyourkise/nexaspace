<?php

namespace App\Filament\Widgets;

use App\Models\Billing;
use App\Models\Device;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Billing::where('status', 'paid')
            ->whereYear('billing_month', Carbon::now()->year)
            ->whereMonth('billing_month', Carbon::now()->month)
            ->sum('amount');

        return [
            Stat::make('Total Tenants', User::where('role', 'tenant')->count())
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
        ];
    }
}
