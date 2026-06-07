<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Billing;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class TenantDashboardWidget extends Widget
{
    protected string $view = 'filament.tenant.widgets.tenant-dashboard-widget';

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isTenant() ?? false;
    }

    protected function getViewData(): array
    {
        $user   = auth()->user();
        $userId = $user->id;

        $unpaidTotal = (int) Billing::where('user_id', $userId)
            ->whereIn('status', ['unpaid', 'throttled'])
            ->sum('amount');

        $unpaidCount = Billing::where('user_id', $userId)
            ->whereIn('status', ['unpaid', 'throttled'])
            ->count();

        $paidCount = Billing::where('user_id', $userId)
            ->where('status', 'paid')
            ->count();

        $totalBills = Billing::where('user_id', $userId)->count();

        $deviceTotal     = $user->devices()->count();
        $deviceActive    = $user->devices()->where('status', 'active')->count();
        $deviceThrottled = $user->devices()->where('status', 'throttled')->count();

        $isThrottled = $deviceThrottled > 0
            || Billing::where('user_id', $userId)->where('status', 'throttled')->exists();

        $juragan = $user->juragan;

        return [
            'userName'        => $user->name,
            'email'           => $user->email,
            'roomNumber'      => $user->room_number,
            'kosName'         => $juragan?->kos_name,
            'dateStr'         => Carbon::now('Asia/Makassar')->locale('id')->isoFormat('dddd, D MMMM Y'),
            'unpaidTotal'     => $unpaidTotal,
            'unpaidCount'     => $unpaidCount,
            'paidCount'       => $paidCount,
            'totalBills'      => $totalBills,
            'deviceTotal'     => $deviceTotal,
            'deviceActive'    => $deviceActive,
            'deviceThrottled' => $deviceThrottled,
            'isThrottled'     => $isThrottled,
        ];
    }
}
