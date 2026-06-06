<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Tagihan Jatuh Tempo</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#d97706;padding:24px 32px;">
                            <span style="color:#ffffff;font-size:20px;font-weight:bold;letter-spacing:1px;">NexaSpace</span>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 8px;font-size:22px;color:#111827;">Pengingat: Tagihan Jatuh Tempo {{ $dueDate }}</h1>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Halo <strong>{{ $juragan->name }}</strong>, berikut daftar anak kos di <strong>{{ $juragan->kos_name }}</strong>
                                yang belum membayar tagihan dan akan jatuh tempo pada <strong>{{ $dueDate }}</strong>.
                            </p>

                            {{-- Warning banner --}}
                            <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:14px 16px;margin-bottom:20px;">
                                <p style="margin:0;font-size:13px;color:#92400e;">
                                    ⚠️ Koneksi WiFi anak kos yang belum bayar setelah H+2 jatuh tempo akan dibatasi secara otomatis.
                                </p>
                            </div>

                            {{-- Unpaid tenants table --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:13px;margin-bottom:24px;">
                                <tr style="background:#f9fafb;">
                                    <th align="left" style="padding:8px 10px;border:1px solid #e5e7eb;color:#374151;">Kamar</th>
                                    <th align="left" style="padding:8px 10px;border:1px solid #e5e7eb;color:#374151;">Nama</th>
                                    <th align="right" style="padding:8px 10px;border:1px solid #e5e7eb;color:#374151;">Tagihan</th>
                                </tr>
                                @foreach ($unpaidTenants as $tenant)
                                    <tr>
                                        <td style="padding:8px 10px;border:1px solid #e5e7eb;color:#1f2937;">{{ $tenant['room_number'] ?? '-' }}</td>
                                        <td style="padding:8px 10px;border:1px solid #e5e7eb;color:#1f2937;">{{ $tenant['name'] }}</td>
                                        <td align="right" style="padding:8px 10px;border:1px solid #e5e7eb;color:#1f2937;">Rp {{ number_format($tenant['amount'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            <a href="{{ $adminUrl }}" style="display:inline-block;background:#d97706;color:#ffffff;font-size:14px;font-weight:bold;padding:12px 24px;border-radius:8px;text-decoration:none;">
                                Kelola Tagihan di Panel
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
