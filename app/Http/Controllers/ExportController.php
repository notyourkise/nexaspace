<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    /**
     * Export billing records as CSV.
     * Respects data isolation: juragan only exports their own anak kos's bills.
     */
    public function billing(Request $request)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['developer', 'juragan'], true)) {
            abort(403);
        }

        $query = Billing::query()
            ->with('user')
            ->orderBy('billing_month', 'desc')
            ->orderBy('created_at', 'desc');

        if ($user->isJuragan()) {
            $query->whereHas('user', fn ($q) => $q->where('juragan_id', $user->id));
        }

        $month = $request->query('month'); // Optional filter: YYYY-MM
        if ($month) {
            [$year, $mon] = explode('-', $month);
            $query->whereYear('billing_month', $year)->whereMonth('billing_month', $mon);
        }

        $billings  = $query->get();
        $filename  = 'tagihan-' . ($month ?? Carbon::now()->format('Y-m')) . '.csv';

        return response()->streamDownload(function () use ($billings): void {
            $handle = fopen('php://output', 'w');

            // BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Nama Anak Kos', 'Nomor Kamar', 'Bulan Tagihan', 'Jatuh Tempo', 'Nominal (Rp)', 'Status']);

            foreach ($billings as $billing) {
                fputcsv($handle, [
                    $billing->user?->name ?? '-',
                    $billing->user?->room_number ?? '-',
                    Carbon::parse($billing->billing_month)->format('M Y'),
                    Carbon::parse($billing->due_date)->format('d/m/Y'),
                    $billing->amount,
                    match ($billing->status) {
                        'paid'      => 'Lunas',
                        'unpaid'    => 'Belum Lunas',
                        'throttled' => 'Dibatasi',
                        default     => $billing->status,
                    },
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Export subscription records as CSV.
     * Developer sees all; juragan sees only their own.
     */
    public function subscription(Request $request)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['developer', 'juragan'], true)) {
            abort(403);
        }

        $query = Subscription::query()
            ->with('juragan')
            ->orderBy('subscription_month', 'desc');

        if ($user->isJuragan()) {
            $query->where('juragan_id', $user->id);
        }

        $subscriptions = $query->get();
        $filename      = 'langganan-' . Carbon::now()->format('Y-m') . '.csv';

        return response()->streamDownload(function () use ($subscriptions): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Juragan', 'Nama Kos', 'Paket', 'Bulan', 'Jatuh Tempo', 'Nominal (Rp)', 'Status']);

            foreach ($subscriptions as $sub) {
                fputcsv($handle, [
                    $sub->juragan?->name ?? '-',
                    $sub->juragan?->kos_name ?? '-',
                    strtoupper($sub->juragan?->plan ?? '-'),
                    Carbon::parse($sub->subscription_month)->format('M Y'),
                    Carbon::parse($sub->due_date)->format('d/m/Y'),
                    $sub->amount,
                    match ($sub->status) {
                        'paid'   => 'Lunas',
                        'unpaid' => 'Belum Lunas',
                        'overdue' => 'Menunggak',
                        default  => $sub->status,
                    },
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
