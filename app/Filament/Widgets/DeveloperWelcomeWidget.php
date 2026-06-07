<?php

namespace App\Filament\Widgets;

use App\Models\Billing;
use App\Models\Device;
use App\Models\Registration;
use App\Models\Subscription;
use App\Models\User;
use App\Services\MikroTikService;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeveloperWelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.developer-welcome-widget';

    protected static ?int $sort = -10;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isDeveloper() ?? false;
    }

    protected function getViewData(): array
    {
        $now = Carbon::now('Asia/Makassar');

        // ── Stats ──────────────────────────────────────────────
        $totalJuragan   = User::where('role', 'juragan')->count();
        $totalAnakKos   = User::where('role', 'tenant')->count();
        $activeDevices  = Device::where('status', 'active')->count();
        $unpaidBills    = Billing::where('status', 'unpaid')->count();
        $failedJobs     = DB::table('failed_jobs')->count();
        $pendaftaranBaru = Registration::where('status', 'pending')->count();

        $totalRevenue = Billing::where('status', 'paid')
            ->whereYear('billing_month', $now->year)
            ->whereMonth('billing_month', $now->month)
            ->sum('amount');

        $subscriptionAktif = Subscription::where('status', 'paid')
            ->whereYear('subscription_month', $now->year)
            ->whereMonth('subscription_month', $now->month)
            ->count();

        // ── DB health ──────────────────────────────────────────
        $dbHealthy = true;
        try {
            DB::connection()->getPdo();
        } catch (\Exception) {
            $dbHealthy = false;
        }

        // ── MikroTik ───────────────────────────────────────────
        $mikrotik    = app(MikroTikService::class);
        $mkConfigured = $mikrotik->isConfigured();
        $mkConnected  = $mkConfigured && $mikrotik->isConnected();
        $mkInfo       = $mikrotik->connectionInfo();

        // ── Revenue chart — 6 bulan terakhir ──────────────────
        $months = collect(range(5, 0))->map(fn ($i) => $now->copy()->subMonths($i)->startOfMonth());

        $revenues = Billing::query()
            ->where('status', 'paid')
            ->whereDate('billing_month', '>=', $months->first())
            ->selectRaw('YEAR(billing_month) as yr, MONTH(billing_month) as mo, SUM(amount) as total')
            ->groupByRaw('YEAR(billing_month), MONTH(billing_month)')
            ->get()
            ->keyBy(fn ($r) => $r->yr . '-' . str_pad($r->mo, 2, '0', STR_PAD_LEFT));

        $chartLabels = $months->map(fn (Carbon $m) => $m->translatedFormat('M Y'))->toArray();
        $chartValues = $months->map(function (Carbon $m) use ($revenues): int {
            return (int) ($revenues[$m->format('Y') . '-' . $m->format('m')]?->total ?? 0);
        })->values()->toArray();

        // ── SVG chart geometry (viewBox 0 0 580 200) ──────────
        $cLeft   = 52;
        $cTop    = 8;
        $cRight  = 572;
        $cBottom = 155;
        $cW      = $cRight - $cLeft;
        $cH      = $cBottom - $cTop;
        $maxV    = max(array_merge([1], $chartValues));
        $count   = count($chartValues);

        $pts = array_map(function ($val, $i) use ($count, $cLeft, $cTop, $cBottom, $cW, $cH, $maxV) {
            $x = $count > 1
                ? round($cLeft + ($i / ($count - 1)) * $cW, 2)
                : $cLeft;
            $y = round($cBottom - ($val / $maxV) * $cH, 2);
            return compact('x', 'y');
        }, $chartValues, array_keys($chartValues));

        // Smooth cubic bezier path
        $line = "M {$pts[0]['x']},{$pts[0]['y']}";
        for ($i = 1; $i < count($pts); $i++) {
            $dx  = ($pts[$i]['x'] - $pts[$i - 1]['x']) * 0.4;
            $cp1x = round($pts[$i - 1]['x'] + $dx, 2);
            $cp2x = round($pts[$i]['x']     - $dx, 2);
            $line .= " C {$cp1x},{$pts[$i-1]['y']} {$cp2x},{$pts[$i]['y']} {$pts[$i]['x']},{$pts[$i]['y']}";
        }
        $area  = $line . " L {$pts[$count-1]['x']},{$cBottom} L {$pts[0]['x']},{$cBottom} Z";
        $yAt   = fn ($pct) => round($cBottom - $pct * $cH, 2);

        // Short-format revenue labels
        $fmtShort = function (float $val): string {
            if ($val >= 1_000_000) {
                return 'Rp ' . rtrim(rtrim(number_format($val / 1_000_000, 1, '.', ''), '0'), '.') . 'jt';
            }
            if ($val >= 1_000) {
                return 'Rp ' . number_format($val / 1_000, 0, '.', '') . 'rb';
            }
            return $val > 0 ? 'Rp ' . $val : '0';
        };

        $yLabels = [
            ['y' => $yAt(0),    'text' => $fmtShort(0)],
            ['y' => $yAt(0.5),  'text' => $fmtShort($maxV * 0.5)],
            ['y' => $yAt(1),    'text' => $fmtShort((float) $maxV)],
        ];

        return [
            // identity
            'userName'       => auth()->user()?->name ?? 'Developer',
            'dateStr'        => $now->locale('id')->isoFormat('D MMMM YYYY'),
            // health
            'dbHealthy'      => $dbHealthy,
            // stats
            'totalJuragan'   => $totalJuragan,
            'totalAnakKos'   => $totalAnakKos,
            'activeDevices'  => $activeDevices,
            'unpaidBills'    => $unpaidBills,
            'failedJobs'     => $failedJobs,
            'pendaftaranBaru' => $pendaftaranBaru,
            'totalRevenue'       => $totalRevenue,
            'subscriptionAktif'  => $subscriptionAktif,
            // mikrotik
            'mkConfigured'   => $mkConfigured,
            'mkConnected'    => $mkConnected,
            'mkHost'         => $mkInfo['host'] ?: '—',
            'mkPort'         => $mkInfo['port'],
            'mkUser'         => $mkInfo['user'],
            // chart
            'chartLabels'    => $chartLabels,
            'chartValues'    => $chartValues,
            'svgPts'         => $pts,
            'svgLine'        => $line,
            'svgArea'        => $area,
            'svgYLabels'     => $yLabels,
            'svgCBottom'     => $cBottom,
            'svgCLeft'       => $cLeft,
            'svgCRight'      => $cRight,
            'svgCTop'        => $cTop,
        ];
    }
}
