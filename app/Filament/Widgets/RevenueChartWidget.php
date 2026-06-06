<?php

namespace App\Filament\Widgets;

use App\Models\Billing;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pendapatan 6 Bulan Terakhir';

    protected static ?int $sort = 20;

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
                    'backgroundColor' => 'rgba(48, 109, 41, 0.15)',
                    'borderColor'     => '#306D29',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointBackgroundColor' => '#306D29',
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
                    'ticks'       => [
                        'callback' => "function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }",
                    ],
                ],
            ],
        ];
    }
}
