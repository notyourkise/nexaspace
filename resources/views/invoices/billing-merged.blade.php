<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Invoice Gabungan</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #1a1a1a; background: #fff; }

    .page { padding: 40px 48px; }

    /* ── Header ── */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; border-bottom: 2px solid #306D29; padding-bottom: 24px; }
    .brand-name { font-size: 22px; font-weight: 700; color: #306D29; letter-spacing: -0.5px; }
    .brand-tagline { font-size: 11px; color: #666; margin-top: 2px; }
    .invoice-meta { text-align: right; }
    .invoice-title { font-size: 20px; font-weight: 700; color: #306D29; text-transform: uppercase; letter-spacing: 1px; }
    .invoice-number { font-size: 12px; color: #555; margin-top: 4px; }
    .invoice-date { font-size: 11px; color: #888; margin-top: 2px; }

    /* ── Parties ── */
    .parties { display: flex; justify-content: space-between; margin-bottom: 32px; gap: 24px; }
    .party-block { flex: 1; }
    .party-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #888; margin-bottom: 6px; }
    .party-name { font-size: 14px; font-weight: 700; color: #1a1a1a; }
    .party-detail { font-size: 12px; color: #555; margin-top: 2px; line-height: 1.5; }

    /* ── Table ── */
    .billing-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .billing-table thead tr { background: #306D29; color: #fff; }
    .billing-table thead th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .billing-table thead th.right { text-align: right; }
    .billing-table tbody tr { border-bottom: 1px solid #e5e5e5; }
    .billing-table tbody tr:last-child { border-bottom: none; }
    .billing-table tbody td { padding: 11px 14px; font-size: 13px; color: #333; }
    .billing-table tbody td.right { text-align: right; }
    .billing-table tfoot tr { border-top: 2px solid #306D29; }
    .billing-table tfoot td { padding: 10px 14px; font-size: 13px; font-weight: 700; color: #1a1a1a; }
    .billing-table tfoot td.right { text-align: right; }

    /* ── Status badge ── */
    .status-badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-paid { background: #d1fae5; color: #065f46; }
    .status-unpaid { background: #fef3c7; color: #92400e; }
    .status-throttled { background: #fee2e2; color: #991b1b; }

    /* ── Summary boxes ── */
    .summary { display: flex; gap: 12px; margin-bottom: 24px; }
    .summary-box { flex: 1; border: 1px solid #e5e5e5; border-radius: 8px; padding: 14px 16px; }
    .summary-box.paid-box { border-color: #bbf7d0; background: #f0fdf4; }
    .summary-box.unpaid-box { border-color: #fde68a; background: #fffbeb; }
    .summary-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #888; margin-bottom: 5px; }
    .summary-value { font-size: 18px; font-weight: 800; color: #1a1a1a; }
    .summary-box.paid-box .summary-label { color: #166534; }
    .summary-box.paid-box .summary-value { color: #166534; }
    .summary-box.unpaid-box .summary-label { color: #92400e; }
    .summary-box.unpaid-box .summary-value { color: #92400e; }

    /* ── QRIS ── */
    .qris-block { margin-bottom: 28px; display: flex; align-items: flex-start; gap: 20px; border: 1px solid #e5e5e5; border-radius: 10px; padding: 16px; background: #fafafa; }

    /* ── Footer ── */
    .footer { border-top: 1px solid #e5e5e5; padding-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
    .footer-note { font-size: 11px; color: #888; max-width: 320px; line-height: 1.5; }
    .footer-brand { font-size: 11px; color: #306D29; font-weight: 600; text-align: right; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand-name">NexaSpace</div>
            <div class="brand-tagline">Platform Manajemen Kos Digital</div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">Invoice Gabungan</div>
            <div class="invoice-number">{{ $billings->count() }} tagihan &mdash; {{ \Carbon\Carbon::parse($billings->first()->billing_month)->locale('id')->isoFormat('MMMM Y') }} s/d {{ \Carbon\Carbon::parse($billings->last()->billing_month)->locale('id')->isoFormat('MMMM Y') }}</div>
            <div class="invoice-date">Dicetak: {{ $generatedAt->format('d M Y, H:i') }} WITA</div>
        </div>
    </div>

    {{-- Parties --}}
    <div class="parties">
        <div class="party-block">
            <div class="party-label">Ditagihkan Kepada</div>
            <div class="party-name">{{ $tenant->name }}</div>
            <div class="party-detail">
                @if($tenant->room_number) Kamar {{ $tenant->room_number }}<br>@endif
                @if($juragan) {{ $juragan->kos_name }}<br>@endif
                {{ $tenant->email }}
                @if($tenant->phone_number)<br>{{ $tenant->phone_number }}@endif
            </div>
        </div>
        <div class="party-block" style="text-align: right;">
            <div class="party-label">Dikelola Oleh</div>
            <div class="party-name">{{ $juragan?->kos_name ?? 'NexaSpace' }}</div>
            @if($juragan)
            <div class="party-detail">
                {{ $juragan->email }}<br>
                @if($juragan->phone_number){{ $juragan->phone_number }}@endif
            </div>
            @endif
        </div>
    </div>

    {{-- Billing table --}}
    <table class="billing-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Periode</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($billings as $i => $billing)
            @php
                $statusClass = match($billing->status) {
                    'paid'      => 'status-paid',
                    'unpaid'    => 'status-unpaid',
                    'throttled' => 'status-throttled',
                    default     => 'status-unpaid',
                };
                $statusLabel = match($billing->status) {
                    'paid'      => 'Lunas',
                    'unpaid'    => 'Belum Lunas',
                    'throttled' => 'Dibatasi',
                    default     => strtoupper($billing->status),
                };
            @endphp
            <tr>
                <td style="color:#999; font-size:11px;">{{ $i + 1 }}</td>
                <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($billing->billing_month)->locale('id')->isoFormat('MMMM Y') }}</td>
                <td style="color: #555;">{{ \Carbon\Carbon::parse($billing->due_date)->format('d M Y') }}</td>
                <td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                <td class="right">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="font-size: 13px; font-weight: 700;">Total Keseluruhan</td>
                <td class="right" style="font-size: 15px; color: #306D29;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Summary boxes --}}
    @if($billings->count() > 1)
    <div class="summary">
        <div class="summary-box paid-box">
            <div class="summary-label">Sudah Lunas</div>
            <div class="summary-value">Rp {{ number_format($paidAmount, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box unpaid-box">
            <div class="summary-label">Belum Lunas</div>
            <div class="summary-value">Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Total Tagihan</div>
            <div class="summary-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
        </div>
    </div>
    @endif

    {{-- QRIS (only when juragan has one and there are unpaid billings) --}}
    @if($juragan?->qris_image && $unpaidAmount > 0)
    <div class="qris-block">
        <img src="{{ public_path('storage/' . $juragan->qris_image) }}"
             alt="QRIS" style="width: 100px; height: 100px; object-fit: contain; border-radius: 6px; border: 1px solid #ddd; background: #fff;">
        <div style="font-size: 12px; color: #555;">
            <p style="font-weight: 700; color: #1a1a1a; margin-bottom: 6px;">Bayar via QRIS</p>
            <p style="margin-bottom: 4px;">Scan kode QR menggunakan m-banking atau dompet digital (GoPay, OVO, Dana, ShopeePay, dll.)</p>
            <p style="color: #306D29; font-weight: 600;">Nominal Belum Lunas: Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</p>
            @if($juragan->phone_number)
            <p style="margin-top: 6px; color: #888;">Konfirmasi ke: {{ $juragan->phone_number }}</p>
            @endif
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-note">
            Dokumen ini merangkum {{ $billings->count() }} tagihan kos.
            Pembayaran dilakukan via transfer bank atau QRIS sesuai instruksi pengelola kos.
            Hubungi pengelola jika ada pertanyaan.
        </div>
        <div class="footer-brand">
            nexaspace.site<br>
            <span style="color:#888; font-weight: 400; font-size: 10px;">Platform Kos Digital #1 Indonesia</span>
        </div>
    </div>

</div>
</body>
</html>
