<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Paket {{ strtoupper($registration->plan) }} — NexaSpace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; background: #0a0a0a; color: #fff; font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; overflow: hidden; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #2a2a2a; border-radius: 4px; }
    </style>
</head>
<body>

{{-- ── Navbar ─────────────────────────────────────────────────────────── --}}
<nav style="height:52px; border-bottom:1px solid #1a1a1a; padding:0 40px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
    <a href="{{ route('daftar', $registration->plan) }}"
       style="display:flex;align-items:center;gap:8px;color:#666;font-size:13px;font-weight:500;text-decoration:none;transition:color .15s;"
       onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#666'">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Kembali ke Form
    </a>
    <span style="color:#fff;font-weight:700;font-size:17px;letter-spacing:-.3px;">NexaSpace</span>
</nav>

{{-- ── Main grid ───────────────────────────────────────────────────────── --}}
<div style="height:calc(100vh - 52px); display:grid; grid-template-columns:1fr 380px; overflow:hidden;">

    {{-- ── LEFT: Payment instructions + bank list ─────────────────────── --}}
    <section style="padding:36px 52px; border-right:1px solid #1a1a1a; overflow-y:auto; display:flex; flex-direction:column; gap:0;">

        <p style="font-size:10px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:#555;">Pembayaran Manual</p>

        <h1 style="margin-top:12px;font-size:28px;font-weight:900;line-height:1.2;letter-spacing:-.5px;color:#fff;max-width:520px;">
            Transfer bulan pertama<br>untuk mengaktifkan akun
        </h1>

        <p style="margin-top:10px;font-size:13px;color:#666;line-height:1.7;max-width:520px;">
            Pilih salah satu rekening berikut, lakukan transfer sesuai nominal,
            lalu konfirmasi melalui WhatsApp. Tim NexaSpace akan memverifikasi
            pembayaran sebelum akun juragan dan akun anak kos dibuat.
        </p>

        <div style="margin-top:28px;display:flex;flex-direction:column;gap:0;">
            @foreach($banks as $i => $bank)
            <div style="padding:18px 0; {{ $i > 0 ? 'border-top:1px solid #1a1a1a;' : '' }} display:grid; grid-template-columns:140px 1fr 1fr; align-items:center; gap:20px;">
                {{-- Bank name --}}
                <div>
                    <span style="font-size:22px;font-weight:900;color:#fff;letter-spacing:-.3px;">{{ $bank['logo'] }}</span>
                    <span style="display:none;">{{ $bank['name'] }}</span>
                </div>
                {{-- Account number --}}
                <div>
                    <p style="font-size:9px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#444;margin-bottom:5px;">Nomor Rekening</p>
                    <p style="font-size:17px;font-weight:800;color:#fff;letter-spacing:.5px;font-feature-settings:'tnum';">{{ $bank['account_number'] }}</p>
                </div>
                {{-- Account name --}}
                <div>
                    <p style="font-size:9px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#444;margin-bottom:5px;">Atas Nama</p>
                    <p style="font-size:13px;font-weight:500;color:#aaa;">{{ $bank['account_name'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </section>

    {{-- ── RIGHT: Summary sidebar ──────────────────────────────────────── --}}
    <aside style="background:#0f0f0f; padding:36px 32px; overflow-y:auto; display:flex; flex-direction:column; gap:0;">

        <p style="font-size:10px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:#555;">Ringkasan</p>

        <h2 style="margin-top:10px;font-size:38px;font-weight:900;letter-spacing:-1px;color:#fff;">{{ $planData['label'] }}</h2>

        <div style="margin-top:24px;height:1px;background:#1e1e1e;"></div>

        <div style="margin-top:20px;display:flex;flex-direction:column;gap:16px;">
            <div>
                <p style="font-size:9px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#444;margin-bottom:4px;">ID Pendaftaran</p>
                <p style="font-size:16px;font-weight:800;color:#fff;letter-spacing:.5px;">REG-{{ $registration->id }}</p>
            </div>
            <div>
                <p style="font-size:9px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#444;margin-bottom:4px;">Nama Kos</p>
                <p style="font-size:16px;font-weight:800;color:#fff;">{{ $registration->kos_name }}</p>
            </div>
        </div>

        <div style="margin-top:20px;height:1px;background:#1e1e1e;"></div>

        <div style="margin-top:20px;">
            <p style="font-size:9px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#444;margin-bottom:6px;">Nominal Transfer</p>
            <p style="font-size:32px;font-weight:900;letter-spacing:-.5px;color:#fff;">Rp {{ number_format($amount, 0, ',', '.') }}</p>
        </div>

        <div style="margin-top:24px;display:flex;align-items:flex-start;gap:10px;background:#161616;border:1px solid #222;border-radius:8px;padding:14px;">
            <svg width="18" height="18" style="flex-shrink:0;margin-top:1px;" fill="none" stroke="#666" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 12a8.25 8.25 0 11-15.218-4.452A8.25 8.25 0 0120.25 12z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 20.25L5.25 21l.75-2.25"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75c.75 2.25 2.25 3.75 4.5 4.5"/>
            </svg>
            <p style="font-size:12px;color:#555;line-height:1.6;">
                Setelah transfer, tekan tombol konfirmasi. WhatsApp akan terbuka dengan detail pendaftaran otomatis.
            </p>
        </div>

        <a href="{{ $confirmationUrl }}" target="_blank" rel="noopener"
           style="margin-top:16px;display:flex;align-items:center;justify-content:center;gap:8px;background:#fff;color:#000;border-radius:8px;padding:13px 20px;font-size:14px;font-weight:800;text-decoration:none;transition:background .15s;letter-spacing:-.1px;"
           onmouseover="this.style.background='#e5e5e5'" onmouseout="this.style.background='#fff'">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 12a8.25 8.25 0 11-15.218-4.452A8.25 8.25 0 0120.25 12z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 20.25L5.25 21l.75-2.25"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75c.75 2.25 2.25 3.75 4.5 4.5"/>
            </svg>
            Konfirmasi via WhatsApp
        </a>

        <div style="margin-top:18px;display:flex;align-items:center;gap:12px;">
            <div style="flex:1;height:1px;background:#1e1e1e;"></div>
            <span style="font-size:12px;color:#333;">atau</span>
            <div style="flex:1;height:1px;background:#1e1e1e;"></div>
        </div>

        <a href="{{ route('home') }}"
           style="margin-top:14px;display:flex;align-items:center;justify-content:center;gap:6px;font-size:13px;font-weight:700;color:#444;text-decoration:none;transition:color .15s;"
           onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#444'">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
            </svg>
            Kembali ke Beranda
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" style="margin-left:2px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>

    </aside>

</div>

</body>
</html>
