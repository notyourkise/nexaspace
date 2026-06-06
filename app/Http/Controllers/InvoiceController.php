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
     * Generate a PDF invoice for a billing record.
     *
     * Access rules:
     *  - developer  → any billing
     *  - juragan    → only billings of their own anak kos
     *  - tenant     → only their own billing
     */
    public function download(Request $request, Billing $billing): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $this->authorizeAccess($user, $billing);

        $billing->load('user.juragan');

        $pdf = Pdf::loadView('invoices.billing', [
            'billing'     => $billing,
            'tenant'      => $billing->user,
            'juragan'     => $billing->user->juragan,
            'generatedAt' => Carbon::now(),
        ])->setPaper('a4');

        $filename = 'invoice-' . $billing->id . '-' . Carbon::parse($billing->billing_month)->format('Y-m') . '.pdf';

        return $pdf->download($filename);
    }

    private function authorizeAccess($user, Billing $billing): void
    {
        if ($user->isDeveloper()) {
            return;
        }

        if ($user->isJuragan()) {
            // Juragan may only view bills of their own anak kos.
            if ($billing->user?->juragan_id !== $user->id) {
                abort(403);
            }
            return;
        }

        if ($user->isTenant()) {
            // Tenant may only view their own billing.
            if ($billing->user_id !== $user->id) {
                abort(403);
            }
            return;
        }

        abort(403);
    }
}
