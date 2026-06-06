<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Ditangguhkan</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#dc2626;padding:24px 32px;">
                            <span style="color:#ffffff;font-size:20px;font-weight:bold;letter-spacing:1px;">NexaSpace</span>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 8px;font-size:22px;color:#111827;">Akun {{ $juragan->kos_name }} Ditangguhkan</h1>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Halo <strong>{{ $juragan->name }}</strong>, akses panel admin dan portal anak kos untuk <strong>{{ $juragan->kos_name }}</strong>
                                telah ditangguhkan karena tagihan langganan NexaSpace belum dibayar melewati batas waktu.
                            </p>

                            {{-- Invoice card --}}
                            <div style="border:1px solid #fecaca;border-radius:10px;padding:20px;margin-bottom:24px;background:#fef2f2;">
                                <p style="margin:0 0 10px;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#dc2626;">Tagihan Tertunggak</p>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="font-size:13px;color:#6b7280;">Paket</td>
                                        <td align="right" style="font-size:13px;font-weight:bold;color:#111827;">{{ strtoupper($juragan->plan) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:13px;color:#6b7280;padding-top:8px;">Jumlah</td>
                                        <td align="right" style="font-size:16px;font-weight:bold;color:#dc2626;padding-top:8px;">Rp {{ number_format($subscription->amount, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Segera lakukan pembayaran dan konfirmasi kepada tim NexaSpace via WhatsApp. Akses panel akan dipulihkan otomatis setelah pembayaran diverifikasi.
                            </p>

                            <p style="margin:0;font-size:13px;line-height:1.6;color:#6b7280;">
                                Butuh bantuan? Hubungi tim NexaSpace.
                            </p>
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
