<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koneksi Anak Kos Dibatasi</title>
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
                            <h1 style="margin:0 0 8px;font-size:22px;color:#111827;">Koneksi WiFi Dibatasi Otomatis</h1>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Halo <strong>{{ $juragan->name }}</strong>, sistem NexaSpace telah membatasi koneksi WiFi untuk
                                <strong>{{ count($throttledTenants) }} anak kos</strong> di <strong>{{ $juragan->kos_name }}</strong>
                                karena tagihan belum dibayar melewati batas waktu.
                            </p>

                            {{-- Throttled tenants table --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:13px;margin-bottom:24px;">
                                <tr style="background:#fef2f2;">
                                    <th align="left" style="padding:8px 10px;border:1px solid #fecaca;color:#374151;">Kamar</th>
                                    <th align="left" style="padding:8px 10px;border:1px solid #fecaca;color:#374151;">Nama</th>
                                </tr>
                                @foreach ($throttledTenants as $tenant)
                                    <tr>
                                        <td style="padding:8px 10px;border:1px solid #fecaca;color:#1f2937;">{{ $tenant['room_number'] ?? '-' }}</td>
                                        <td style="padding:8px 10px;border:1px solid #fecaca;color:#1f2937;">{{ $tenant['name'] }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Koneksi akan dipulihkan secara otomatis setelah Anda menandai tagihan sebagai <strong>Lunas</strong> di panel admin.
                            </p>

                            <a href="{{ $adminUrl }}" style="display:inline-block;background:#dc2626;color:#ffffff;font-size:14px;font-weight:bold;padding:12px 24px;border-radius:8px;text-decoration:none;">
                                Tandai Lunas di Panel
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
