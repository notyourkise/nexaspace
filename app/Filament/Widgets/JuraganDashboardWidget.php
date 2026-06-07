<?php

namespace App\Filament\Widgets;

use App\Models\Billing;
use App\Models\Device;
use App\Models\MaintenanceTicket;
use App\Models\Subscription;
use App\Models\User;
use App\Services\MikroTikService;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class JuraganDashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.juragan-dashboard-widget';

    protected static ?int $sort = -10;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isJuragan() ?? false;
    }

    protected function getViewData(): array
    {
        $juragan = auth()->user();
        $now = Carbon::now('Asia/Makassar');

        $tenantIds = User::query()
            ->where('role', 'tenant')
            ->where('juragan_id', $juragan->id)
            ->pluck('id');

        $tenantCount = $tenantIds->count();
        $quota = (int) ($juragan->room_quota ?: 0);
        $quotaSisa = $quota > 0 ? max(0, $quota - $tenantCount) : 0;
        $quotaPct = $quota > 0 ? min(100, round(($tenantCount / $quota) * 100)) : 0;

        $activeDevices = Device::whereIn('user_id', $tenantIds)->where('status', 'active')->count();
        $throttledDevices = Device::whereIn('user_id', $tenantIds)->where('status', 'throttled')->count();
        $unpaidBills = Billing::whereIn('user_id', $tenantIds)->where('status', 'unpaid')->count();
        $throttledBills = Billing::whereIn('user_id', $tenantIds)->where('status', 'throttled')->count();

        $totalRevenue = Billing::whereIn('user_id', $tenantIds)
            ->where('status', 'paid')
            ->whereYear('billing_month', $now->year)
            ->whereMonth('billing_month', $now->month)
            ->sum('amount');

        $openReports = MaintenanceTicket::where('juragan_id', $juragan->id)
            ->whereIn('status', [MaintenanceTicket::STATUS_OPEN, MaintenanceTicket::STATUS_REVIEWED, MaintenanceTicket::STATUS_IN_PROGRESS])
            ->count();

        $resolvedReports = MaintenanceTicket::where('juragan_id', $juragan->id)
            ->where('status', MaintenanceTicket::STATUS_RESOLVED)
            ->whereYear('resolved_at', $now->year)
            ->whereMonth('resolved_at', $now->month)
            ->count();

        $subscription = Subscription::query()
            ->where('juragan_id', $juragan->id)
            ->whereYear('subscription_month', $now->year)
            ->whereMonth('subscription_month', $now->month)
            ->first();

        [$subscriptionLabel, $subscriptionTone] = match ($subscription?->status) {
            'paid' => ['Aktif', 'green'],
            'unpaid' => ['Belum Lunas', 'amber'],
            'overdue' => ['Menunggak', 'red'],
            default => ['Belum Ada Tagihan', 'gray'],
        };

        $today = $now->copy()->startOfDay();
        $billingDueToday = User::query()
            ->whereIn('id', $tenantIds)
            ->whereNotNull('move_in_date')
            ->whereRaw('DAY(move_in_date) = ?', [$today->day])
            ->where('monthly_rate', '>', 0)
            ->count();

        $mikrotik = app(MikroTikService::class);
        $mkConfigured = $mikrotik->isConfigured();
        $mkConnected = $mkConfigured && $mikrotik->isConnected();
        $mkInfo = $mikrotik->connectionInfo();

        $months = collect(range(5, 0))->map(fn ($i) => $now->copy()->subMonths($i)->startOfMonth());
        $revenues = Billing::query()
            ->whereIn('user_id', $tenantIds)
            ->where('status', 'paid')
            ->whereDate('billing_month', '>=', $months->first())
            ->selectRaw('YEAR(billing_month) as yr, MONTH(billing_month) as mo, SUM(amount) as total')
            ->groupByRaw('YEAR(billing_month), MONTH(billing_month)')
            ->get()
            ->keyBy(fn ($r) => $r->yr . '-' . str_pad($r->mo, 2, '0', STR_PAD_LEFT));

        $chartLabels = $months->map(fn (Carbon $m) => $m->translatedFormat('M Y'))->toArray();
        $chartValues = $months->map(fn (Carbon $m): int => (int) ($revenues[$m->format('Y') . '-' . $m->format('m')]?->total ?? 0))->values()->toArray();

        $cLeft = 52;
        $cTop = 8;
        $cRight = 572;
        $cBottom = 155;
        $cW = $cRight - $cLeft;
        $cH = $cBottom - $cTop;
        $maxV = max(array_merge([1], $chartValues));
        $count = count($chartValues);

        $pts = array_map(function ($val, $i) use ($count, $cLeft, $cTop, $cBottom, $cW, $cH, $maxV) {
            $x = $count > 1 ? round($cLeft + ($i / ($count - 1)) * $cW, 2) : $cLeft;
            $y = round($cBottom - ($val / $maxV) * $cH, 2);
            return compact('x', 'y');
        }, $chartValues, array_keys($chartValues));

        $line = "M {$pts[0]['x']},{$pts[0]['y']}";
        for ($i = 1; $i < count($pts); $i++) {
            $dx = ($pts[$i]['x'] - $pts[$i - 1]['x']) * 0.4;
            $cp1x = round($pts[$i - 1]['x'] + $dx, 2);
            $cp2x = round($pts[$i]['x'] - $dx, 2);
            $line .= " C {$cp1x},{$pts[$i - 1]['y']} {$cp2x},{$pts[$i]['y']} {$pts[$i]['x']},{$pts[$i]['y']}";
        }
        $area = $line . " L {$pts[$count - 1]['x']},{$cBottom} L {$pts[0]['x']},{$cBottom} Z";

        $fmtShort = function (float $val): string {
            if ($val >= 1_000_000) return 'Rp ' . rtrim(rtrim(number_format($val / 1_000_000, 1, '.', ''), '0'), '.') . 'jt';
            if ($val >= 1_000) return 'Rp ' . number_format($val / 1_000, 0, '.', '') . 'rb';
            return $val > 0 ? 'Rp ' . $val : '0';
        };

        $yAt = fn ($pct) => round($cBottom - $pct * $cH, 2);

        return [
            'userName' => $juragan->name,
            'kosName' => $juragan->kos_name ?? 'Kos Anda',
            'plan' => strtoupper($juragan->plan ?? '-'),
            'dateStr' => $now->locale('id')->isoFormat('D MMMM YYYY'),
            'tenantCount' => $tenantCount,
            'quota' => $quota,
            'quotaSisa' => $quotaSisa,
            'quotaPct' => $quotaPct,
            'activeDevices' => $activeDevices,
            'throttledDevices' => $throttledDevices,
            'unpaidBills' => $unpaidBills,
            'throttledBills' => $throttledBills,
            'totalRevenue' => $totalRevenue,
            'openReports' => $openReports,
            'resolvedReports' => $resolvedReports,
            'subscriptionLabel' => $subscriptionLabel,
            'subscriptionTone' => $subscriptionTone,
            'subscriptionDue' => $subscription?->due_date?->format('d M Y') ?? '-',
            'billingDueToday' => $billingDueToday,
            'mkConfigured' => $mkConfigured,
            'mkConnected' => $mkConnected,
            'mkHost' => $mkInfo['host'] ?: '-',
            'mkPort' => $mkInfo['port'],
            'mkUser' => $mkInfo['user'],
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'svgPts' => $pts,
            'svgLine' => $line,
            'svgArea' => $area,
            'svgYLabels' => [
                ['y' => $yAt(0), 'text' => $fmtShort(0)],
                ['y' => $yAt(0.5), 'text' => $fmtShort($maxV * 0.5)],
                ['y' => $yAt(1), 'text' => $fmtShort((float) $maxV)],
            ],
            'svgCBottom' => $cBottom,
            'svgCLeft' => $cLeft,
            'svgCRight' => $cRight,
        ];
    }
}
