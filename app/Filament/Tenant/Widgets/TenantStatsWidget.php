<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Billing;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();

        $unpaidTotal = Billing::where('user_id', $userId)
            ->whereIn('status', ['unpaid', 'throttled'])
            ->sum('amount');

        $unpaidCount = Billing::where('user_id', $userId)
            ->whereIn('status', ['unpaid', 'throttled'])
            ->count();

        $paidCount = Billing::where('user_id', $userId)
            ->where('status', 'paid')
            ->count();

        return [
            Stat::make('Total Unpaid Bills', 'Rp ' . number_format($unpaidTotal, 0, ',', '.'))
                ->description("{$unpaidCount} bill(s) outstanding")
                ->icon('heroicon-o-exclamation-circle')
                ->color($unpaidTotal > 0 ? 'warning' : 'success'),

            Stat::make('Bills Paid', $paidCount)
                ->description('All time')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
