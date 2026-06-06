<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun NexaSpace Aktif</title>
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
                            <h1 style="margin:0 0 8px;font-size:22px;color:#111827;">Selamat datang, {{ $juragan->name }}!</h1>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Langganan untuk <strong>{{ $juragan->kos_name }}</strong>
                                (paket <strong>{{ strtoupper($juragan->plan) }}</strong>) telah aktif.
                                Berikut akun Anda dan {{ count($anakKos) }} akun anak kos yang sudah kami siapkan.
                            </p>

                            {{-- Juragan account --}}
                            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:18px;margin-bottom:20px;">
                                <p style="margin:0 0 10px;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#306D29;">Akun Juragan (Admin Kos)</p>
                                <p style="margin:0;font-size:14px;line-height:1.8;color:#1f2937;">
                                    <strong>Email:</strong> {{ $juragan->email }}<br>
                                    <strong>Password:</strong> {{ $juraganPassword }}<br>
                                    <strong>Login:</strong> <a href="{{ $adminLoginUrl }}" style="color:#306D29;">{{ $adminLoginUrl }}</a>
                                </p>
                            </div>

                            {{-- Anak kos accounts --}}
                            <p style="margin:0 0 10px;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#306D29;">Akun Anak Kos ({{ count($anakKos) }})</p>
                            <p style="margin:0 0 12px;font-size:13px;color:#6b7280;">
                                Login anak kos di:
                                <a href="{{ $tenantLoginUrl }}" style="color:#306D29;">{{ $tenantLoginUrl }}</a>
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:13px;">
                                <tr style="background:#f9fafb;">
                                    <th align="left" style="padding:8px 10px;border:1px solid #e5e7eb;color:#374151;">Email</th>
                                    <th align="left" style="padding:8px 10px;border:1px solid #e5e7eb;color:#374151;">Password</th>
                                </tr>
                                @foreach ($anakKos as $akun)
                                    <tr>
                                        <td style="padding:8px 10px;border:1px solid #e5e7eb;color:#1f2937;">{{ $akun['email'] }}</td>
                                        <td style="padding:8px 10px;border:1px solid #e5e7eb;color:#1f2937;font-family:monospace;">{{ $akun['password'] }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            <p style="margin:24px 0 0;font-size:13px;line-height:1.6;color:#6b7280;">
                                Demi keamanan, segera ganti password setelah login pertama.
                                Simpan email ini di tempat yang aman.
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
