<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Invoice Tagihan</title>
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
    .billing-table tbody td { padding: 12px 14px; font-size: 13px; color: #333; }
    .billing-table tbody td.right { text-align: right; }

    /* ── Total ── */
    .totals { display: flex; justify-content: flex-end; margin-bottom: 32px; }
    .totals-box { min-width: 260px; }
    .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #555; border-bottom: 1px solid #eee; }
    .total-row.grand { font-size: 15px; font-weight: 700; color: #1a1a1a; border-bottom: 2px solid #306D29; padding-top: 10px; margin-top: 4px; }
    .total-label { }
    .total-value { font-weight: 600; }

    /* ── Status badge ── */
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-paid { background: #d1fae5; color: #065f46; }
    .status-unpaid { background: #fef3c7; color: #92400e; }
    .status-throttled { background: #fee2e2; color: #991b1b; }

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
            <div class="invoice-title">Invoice</div>
            <div class="invoice-number">#INV-{{ str_pad($billing->id, 6, '0', STR_PAD_LEFT) }}</div>
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

    {{-- Billing details table --}}
    <table class="billing-table">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Periode</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Iuran Kos Bulanan<br><small style="color:#888;">{{ $tenant->room_number ? 'Kamar ' . $tenant->room_number : $tenant->name }}</small></td>
                <td>{{ \Illuminate\Support\Carbon::parse($billing->billing_month)->format('F Y') }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($billing->due_date)->format('d M Y') }}</td>
                <td>
                    @php
                        $statusClass = match($billing->status) {
                            'paid'      => 'status-paid',
                            'unpaid'    => 'status-unpaid',
                            'throttled' => 'status-throttled',
                            default     => 'status-unpaid',
                        };
                        $statusLabel = match($billing->status) {
                            'paid'      => 'LUNAS',
                            'unpaid'    => 'BELUM LUNAS',
                            'throttled' => 'DIBATASI',
                            default     => strtoupper($billing->status),
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </td>
                <td class="right" style="font-weight: 600;">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals">
        <div class="totals-box">
            <div class="total-row grand">
                <span class="total-label">Total Tagihan</span>
                <span class="total-value">Rp {{ number_format($billing->amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- QRIS (only when juragan has one and billing is not yet paid) --}}
    @if ($juragan?->qris_image && $billing->status !== 'paid')
    <div style="margin-bottom: 28px; display: flex; align-items: flex-start; gap: 20px; border: 1px solid #e5e5e5; border-radius: 10px; padding: 16px; background: #fafafa;">
        <img src="{{ public_path('storage/' . $juragan->qris_image) }}"
             alt="QRIS" style="width: 100px; height: 100px; object-fit: contain; border-radius: 6px; border: 1px solid #ddd; background: #fff;">
        <div style="font-size: 12px; color: #555;">
            <p style="font-weight: 700; color: #1a1a1a; margin-bottom: 6px;">Bayar via QRIS</p>
            <p style="margin-bottom: 4px;">Scan kode QR menggunakan m-banking atau dompet digital (GoPay, OVO, Dana, ShopeePay, dll.)</p>
            <p style="color: #306D29; font-weight: 600;">Nominal: Rp {{ number_format($billing->amount, 0, ',', '.') }}</p>
            @if($juragan->phone_number)
            <p style="margin-top: 6px; color: #888;">Konfirmasi ke: {{ $juragan->phone_number }}</p>
            @endif
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-note">
            Pembayaran dilakukan via transfer bank atau QRIS sesuai instruksi dari pengelola kos.
            Hubungi pengelola jika ada pertanyaan mengenai tagihan ini.
        </div>
        <div class="footer-brand">
            nexaspace.site<br>
            <span style="color:#888; font-weight: 400; font-size: 10px;">Platform Kos Digital #1 Indonesia</span>
        </div>
    </div>

</div>
</body>
</html>
