<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    /**
     * Generate a PDF invoice for a single billing record.
     *
     * Access rules:
     *  - developer  → any billing
     *  - juragan    → only billings of their own anak kos
     *  - tenant     → only their own billing
     */
    public function download(Request $request, Billing $billing): Response
    {
        $user = $request->user();
        if (! $user) abort(403);

        $this->authorizeAccess($user, $billing);

        $billing->load('user.juragan');

        $tenant = $billing->user;
        abort_if(! $tenant, 404);

        $pdf = Pdf::loadView('invoices.billing', [
            'billing'     => $billing,
            'tenant'      => $tenant,
            'juragan'     => $tenant->juragan,
            'generatedAt' => Carbon::now(),
        ])->setPaper('a4');

        $filename = 'invoice-' . $billing->id . '-' . Carbon::parse($billing->billing_month)->format('Y-m') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate a single merged PDF invoice for multiple billing records.
     * All billings must belong to the same tenant.
     *
     * Query param: ids = comma-separated billing IDs (max 36 months)
     */
    public function downloadMerged(Request $request): Response
    {
        $user = $request->user();
        if (! $user) abort(403);

        $rawIds = array_filter(
            array_map('intval', explode(',', $request->query('ids', '')))
        );

        abort_if(empty($rawIds), 400);

        // Cap at 36 months for safety
        $ids = array_slice(array_unique(array_values($rawIds)), 0, 36);

        $billings = Billing::with('user.juragan')
            ->whereIn('id', $ids)
            ->orderBy('billing_month')
            ->get();

        abort_if($billings->isEmpty(), 404);

        // Authorize each billing
        foreach ($billings as $billing) {
            $this->authorizeAccess($user, $billing);
        }

        // All billings must belong to the same tenant
        abort_if($billings->pluck('user_id')->unique()->count() > 1, 422);

        $tenant  = $billings->first()->user;
        abort_if(! $tenant, 404);
        $juragan = $tenant->juragan;
        $totalAmount = $billings->sum('amount');
        $paidAmount  = $billings->where('status', 'paid')->sum('amount');
        $unpaidAmount = $totalAmount - $paidAmount;

        $monthFrom = Carbon::parse($billings->first()->billing_month)->format('Y-m');
        $monthTo   = Carbon::parse($billings->last()->billing_month)->format('Y-m');
        $filename  = "invoice-gabungan-{$monthFrom}-sd-{$monthTo}.pdf";

        $pdf = Pdf::loadView('invoices.billing-merged', [
            'billings'     => $billings,
            'tenant'       => $tenant,
            'juragan'      => $juragan,
            'totalAmount'  => $totalAmount,
            'paidAmount'   => $paidAmount,
            'unpaidAmount' => $unpaidAmount,
            'generatedAt'  => Carbon::now(),
        ])->setPaper('a4');

        return $pdf->download($filename);
    }

    private function authorizeAccess($user, Billing $billing): void
    {
        if ($user->isDeveloper()) return;

        if ($user->isJuragan()) {
            abort_unless($billing->user?->juragan_id === $user->id, 403);
            return;
        }

        if ($user->isTenant()) {
            abort_unless($billing->user_id === $user->id, 403);
            return;
        }

        abort(403);
    }
}
