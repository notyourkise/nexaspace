<?php

namespace App\Filament\Widgets;

use App\Models\Billing;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pendapatan 6 Bulan Terakhir';

    protected static ?int $sort = 20;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return false;
    }

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $user   = auth()->user();
        $months = collect(range(5, 0))->map(fn (int $i) => Carbon::now()->subMonths($i)->startOfMonth());

        $query = Billing::query()
            ->where('status', 'paid')
            ->whereDate('billing_month', '>=', $months->first());

        if ($user && $user->isJuragan()) {
            $query->whereHas('user', fn ($q) => $q->where('juragan_id', $user->id));
        }

        $revenues = $query
            ->selectRaw('YEAR(billing_month) as yr, MONTH(billing_month) as mo, SUM(amount) as total')
            ->groupByRaw('YEAR(billing_month), MONTH(billing_month)')
            ->get()
            ->keyBy(fn ($row) => $row->yr . '-' . str_pad($row->mo, 2, '0', STR_PAD_LEFT));

        $labels = $months->map(fn (Carbon $m) => $m->translatedFormat('M Y'))->toArray();
        $data   = $months->map(function (Carbon $m) use ($revenues): int {
            $key = $m->format('Y') . '-' . $m->format('m');
            return (int) ($revenues[$key]?->total ?? 0);
        })->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Pendapatan (Rp)',
                    'data'            => $data,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.10)',
                    'borderColor'     => '#22c55e',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointBackgroundColor' => '#22c55e',
                    'pointRadius'     => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(ctx) { return 'Rp ' + ctx.parsed.y.toLocaleString('id-ID'); }",
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid'        => ['color' => 'rgba(255,255,255,0.05)'],
                    'ticks'       => [
                        'color'    => 'rgba(148,163,184,0.7)',
                        'callback' => "function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }",
                    ],
                ],
                'x' => [
                    'grid'  => ['color' => 'rgba(255,255,255,0.03)'],
                    'ticks' => ['color' => 'rgba(148,163,184,0.7)'],
                ],
            ],
        ];
    }
}
