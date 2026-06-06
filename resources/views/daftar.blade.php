<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Paket {{ strtoupper($selectedPlan) }} — NexaSpace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; background: #0a0a0a; color: #fff; font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; overflow: hidden; }

        .field { width: 100%; background: #111; border: 1px solid #242424; border-radius: 6px; padding: 10px 14px; font-size: 13.5px; color: #fff; outline: none; transition: border-color .15s, box-shadow .15s; -webkit-appearance: none; }
        .field:focus { border-color: rgba(255,255,255,.35); box-shadow: 0 0 0 3px rgba(255,255,255,.06); }
        .field::placeholder { color: #3a3a3a; }
        .field.err { border-color: #ef4444; }
        .field-err { color: #f87171; font-size: 11px; margin-top: 4px; display: none; }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #2a2a2a; border-radius: 4px; }
    </style>
</head>
<body>

{{-- ── Navbar ─────────────────────────────────────────────────────────── --}}
<nav style="height:52px; border-bottom:1px solid #1a1a1a; padding:0 40px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
    <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:8px;color:#666;font-size:13px;font-weight:500;text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#666'">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Kembali ke Beranda
    </a>
    <span style="color:#fff;font-weight:700;font-size:17px;letter-spacing:-.3px;">NexaSpace</span>
</nav>

{{-- ── Main grid ───────────────────────────────────────────────────────── --}}
<div style="height:calc(100vh - 52px); display:grid; grid-template-columns:360px 1fr; overflow:hidden;">

    {{-- ── LEFT: Plan summary ──────────────────────────────────────────── --}}
    <aside style="border-right:1px solid #1a1a1a; padding:32px 36px; overflow-y:auto; display:flex; flex-direction:column; gap:0;">

        <p style="font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#555;">Paket yang Dipilih</p>

        <h1 style="margin-top:12px;font-size:52px;font-weight:900;line-height:1;letter-spacing:-1px;color:#fff;">{{ $planData['label'] }}</h1>

        <div style="margin-top:12px;display:flex;align-items:flex-end;gap:8px;">
            <span style="font-size:28px;font-weight:900;color:#fff;">{{ $planData['price'] }}</span>
            <span style="font-size:13px;color:#555;padding-bottom:3px;">/ {{ $planData['period'] }}</span>
        </div>

        <div style="margin-top:16px;display:flex;align-items:center;gap:10px;background:#111;border:1px solid #222;border-radius:8px;padding:10px 14px;">
            <svg width="14" height="14" fill="none" stroke="#666" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
            </svg>
            <span style="font-size:13px;font-weight:600;color:#ddd;">{{ $planData['quota'] }}</span>
        </div>

        <div style="height:1px;background:#1a1a1a;margin:20px 0;"></div>

        <p style="font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#555;">Yang Anda Dapatkan</p>

        <ul style="margin-top:14px;display:flex;flex-direction:column;gap:10px;list-style:none;">
            @foreach($planData['features'] as $feature)
            <li style="display:flex;align-items:center;gap:10px;font-size:13px;color:#bbb;">
                <span style="width:18px;height:18px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="10" height="10" fill="none" stroke="#000" viewBox="0 0 24 24" stroke-width="3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </span>
                {{ $feature }}
            </li>
            @endforeach
        </ul>

        <div style="margin-top:16px;display:flex;align-items:flex-start;gap:10px;background:#111;border:1px solid #222;border-radius:8px;padding:12px 14px;">
            <svg width="14" height="14" fill="none" stroke="#555" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15.75 9.75M12 3.75l7.5 3v5.25c0 4.125-2.7 7.875-7.5 9-4.8-1.125-7.5-4.875-7.5-9V6.75l7.5-3z"/>
            </svg>
            <p style="font-size:12px;color:#555;line-height:1.6;">Semua paket sudah termasuk onboarding, migrasi data, dan dukungan WhatsApp. Tidak ada biaya tersembunyi.</p>
        </div>

        <div style="height:1px;background:#1a1a1a;margin:20px 0;"></div>

        <p style="font-size:12px;color:#555;font-weight:500;">Ganti paket:</p>
        <div style="margin-top:10px;display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
            @foreach(['lite' => 'LITE', 'pro' => 'PRO', 'custom' => 'CUSTOM'] as $key => $lbl)
            <a href="{{ route('daftar', $key) }}"
               style="display:block;text-align:center;padding:9px;font-size:11px;font-weight:800;border-radius:6px;text-decoration:none;border:1px solid;transition:all .15s;
                      {{ $selectedPlan === $key ? 'background:#fff;color:#000;border-color:#fff;' : 'background:transparent;color:#555;border-color:#242424;' }}"
               onmouseover="{{ $selectedPlan !== $key ? "this.style.color='#ddd';this.style.borderColor='#555';" : '' }}"
               onmouseout="{{ $selectedPlan !== $key ? "this.style.color='#555';this.style.borderColor='#242424';" : '' }}">
                {{ $lbl }}
            </a>
            @endforeach
        </div>

    </aside>

    {{-- ── RIGHT: Form ─────────────────────────────────────────────────── --}}
    <section style="padding:32px 52px;overflow-y:auto;">

        <h2 style="font-size:30px;font-weight:900;letter-spacing:-.5px;color:#fff;">Lengkapi Data Pendaftaran</h2>
        <p style="margin-top:6px;font-size:13px;color:#555;line-height:1.5;">
            @if($selectedPlan === 'custom')
                Isi form berikut dan tim kami akan menghubungi Anda via WhatsApp.
            @else
                Isi form berikut, lalu lanjutkan ke instruksi transfer bulan pertama.
            @endif
        </p>

        <div id="alert-error" style="display:none;margin-top:16px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:12px 16px;">
            <p style="font-size:13px;color:#f87171;">Terjadi kesalahan. Periksa kembali isian Anda.</p>
        </div>

        <form id="reg-form" style="margin-top:22px;" novalidate>
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#888;margin-bottom:6px;letter-spacing:.05em;">NAMA ANDA <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" id="name" placeholder="Contoh: Pak Budi" class="field">
                    <p class="field-err" id="err-name"></p>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#888;margin-bottom:6px;letter-spacing:.05em;">NAMA KOS <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="kos_name" id="kos_name" placeholder="Contoh: Kos Mutiara" class="field">
                    <p class="field-err" id="err-kos_name"></p>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#888;margin-bottom:6px;letter-spacing:.05em;">EMAIL <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="email" id="email" placeholder="email@anda.com" class="field">
                    <p class="field-err" id="err-email"></p>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#888;margin-bottom:6px;letter-spacing:.05em;">NOMOR WHATSAPP <span style="color:#ef4444;">*</span></label>
                    <input type="tel" name="phone" id="phone" placeholder="08xxxxxxxxxx" class="field">
                    <p class="field-err" id="err-phone"></p>
                </div>
            </div>

            <div style="margin-top:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#888;margin-bottom:6px;letter-spacing:.05em;">
                    JUMLAH KAMAR <span style="color:#ef4444;">*</span>
                    @if($planData['max_rooms'] < 999)
                        <span style="color:#444;font-weight:400;font-size:11px;letter-spacing:0;text-transform:none;"> — maks. {{ $planData['max_rooms'] }} kamar</span>
                    @endif
                </label>
                <input type="number" name="room_count" id="room_count"
                       placeholder="Contoh: 20"
                       min="1" max="{{ $planData['max_rooms'] }}"
                       class="field">
                <p class="field-err" id="err-room_count"></p>
            </div>

            <div style="margin-top:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#888;margin-bottom:6px;letter-spacing:.05em;">
                    PESAN / CATATAN <span style="color:#444;font-weight:400;font-size:11px;letter-spacing:0;text-transform:none;">(opsional)</span>
                </label>
                <textarea name="message" id="message" rows="3"
                          placeholder="Tuliskan pertanyaan atau kebutuhan khusus Anda..."
                          class="field" style="resize:none;"></textarea>
                <p class="field-err" id="err-message"></p>
            </div>

            <input type="hidden" name="plan" value="{{ $selectedPlan }}">

            <div style="margin-top:22px;">
                <button type="submit" id="submit-btn"
                        style="width:100%;background:#fff;color:#000;border:none;border-radius:8px;padding:14px 20px;font-size:14px;font-weight:800;cursor:pointer;transition:background .15s,opacity .15s;letter-spacing:-.1px;">
                    <span id="btn-text">
                        @if($selectedPlan === 'custom')
                            Hubungi Tim NexaSpace via WhatsApp
                        @else
                            Daftar &amp; Bayar Sekarang
                        @endif
                    </span>
                    <span id="btn-loading" style="display:none;">Memproses...</span>
                </button>

                <p style="margin-top:12px;display:flex;align-items:center;justify-content:center;gap:6px;font-size:11px;color:#444;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75M6.75 10.5h10.5a1.5 1.5 0 011.5 1.5v6.75a1.5 1.5 0 01-1.5 1.5H6.75a1.5 1.5 0 01-1.5-1.5V12a1.5 1.5 0 011.5-1.5z"/>
                    </svg>
                    @if($selectedPlan === 'custom')
                        Konsultasi paket custom melalui WhatsApp NexaSpace
                    @else
                        Pembayaran manual via transfer bank, lalu konfirmasi melalui WhatsApp
                    @endif
                </p>
            </div>
        </form>
    </section>

</div>

<script>
const PLAN      = '{{ $selectedPlan }}';
const MAX_ROOMS = {{ $planData['max_rooms'] }};
const REGISTER_URL = '{{ route("registration.store") }}';

const btn      = document.getElementById('submit-btn');
const btnText  = document.getElementById('btn-text');
const btnLoad  = document.getElementById('btn-loading');
const alertEl  = document.getElementById('alert-error');

btn.addEventListener('mouseover', () => { if (!btn.disabled) btn.style.background = '#e5e5e5'; });
btn.addEventListener('mouseout',  () => { if (!btn.disabled) btn.style.background = '#fff'; });

function clearErrors() {
    document.querySelectorAll('.field-err').forEach(el => { el.style.display = 'none'; el.textContent = ''; });
    document.querySelectorAll('.field.err').forEach(el => el.classList.remove('err'));
    alertEl.style.display = 'none';
}

function showErr(field, msg) {
    const errEl = document.getElementById('err-' + field);
    const input = document.getElementById(field) || document.querySelector('[name="' + field + '"]');
    if (errEl) { errEl.textContent = msg; errEl.style.display = 'block'; }
    if (input)  input.classList.add('err');
}

function setLoading(on) {
    btn.disabled      = on;
    btn.style.opacity = on ? '.55' : '1';
    btn.style.cursor  = on ? 'not-allowed' : 'pointer';
    btnText.style.display = on ? 'none' : '';
    btnLoad.style.display = on ? '' : 'none';
}

// Clamp room_count on input
document.getElementById('room_count').addEventListener('input', function () {
    if (PLAN !== 'custom' && this.value && parseInt(this.value) > MAX_ROOMS) this.value = MAX_ROOMS;
});

document.getElementById('reg-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    clearErrors();

    const roomVal = parseInt(document.getElementById('room_count').value);
    if (PLAN !== 'custom' && roomVal > MAX_ROOMS) {
        showErr('room_count', `Paket ${PLAN.toUpperCase()} hanya mendukung maks. ${MAX_ROOMS} kamar.`);
        return;
    }

    setLoading(true);
    const body = Object.fromEntries(new FormData(this).entries());

    try {
        const res  = await fetch(REGISTER_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(body),
        });
        const data = await res.json();

        if (res.status === 422 && data.errors) {
            setLoading(false);
            for (const [f, msgs] of Object.entries(data.errors)) showErr(f, msgs[0]);
            return;
        }
        if (!res.ok) throw new Error('Server error');

        window.location.href = data.wa_url || data.payment_url;
    } catch {
        setLoading(false);
        alertEl.style.display = 'block';
    }
});
</script>
</body>
</html>
