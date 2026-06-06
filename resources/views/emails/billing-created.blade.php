<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Bulan {{ $billingMonth }} Dibuat</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#306D29;padding:24px 32px;">
                            <span style="color:#ffffff;font-size:20px;font-weight:bold;letter-spacing:1px;">NexaSpace</span>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 8px;font-size:22px;color:#111827;">Tagihan Bulan {{ $billingMonth }} Sudah Dibuat</h1>
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Halo <strong>{{ $juragan->name }}</strong>, sistem NexaSpace telah membuat tagihan bulanan untuk <strong>{{ $juragan->kos_name }}</strong>.
                            </p>

                            {{-- Summary card --}}
                            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:20px;margin-bottom:24px;background:#f9fafb;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="font-size:13px;color:#6b7280;">Periode Tagihan</td>
                                        <td align="right" style="font-size:13px;font-weight:bold;color:#111827;">{{ $billingMonth }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:13px;color:#6b7280;padding-top:8px;">Jumlah Tagihan Dibuat</td>
                                        <td align="right" style="font-size:13px;font-weight:bold;color:#111827;padding-top:8px;">{{ $billCount }} anak kos</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:13px;color:#6b7280;padding-top:8px;">Total Tagihan</td>
                                        <td align="right" style="font-size:13px;font-weight:bold;color:#306D29;padding-top:8px;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Tagihan sudah terkirim dan menunggu pembayaran dari anak kos. Pantau status pembayaran melalui panel admin.
                            </p>

                            <a href="{{ $adminUrl }}" style="display:inline-block;background:#306D29;color:#ffffff;font-size:14px;font-weight:bold;padding:12px 24px;border-radius:8px;text-decoration:none;">
                                Lihat Tagihan di Panel
                            </a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f9fafb;padding:18px 32px;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;">
                                Email otomatis dari NexaSpace. Jangan balas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
