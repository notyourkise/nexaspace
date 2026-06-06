<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Paket {{ strtoupper($selectedPlan) }} - NexaSpace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; }
        .field-error { color: #dc2626; font-size: 0.75rem; margin-top: 0.35rem; display: none; }
        .input-error { border-color: #dc2626 !important; }
        .form-field {
            width: 100%;
            border: 1px solid #d9dee7;
            border-radius: 0.75rem;
            background: #fff;
            padding: 0.95rem 1rem;
            font-size: 0.95rem;
            color: #111827;
            transition: border-color .18s ease, box-shadow .18s ease;
        }
        .form-field:focus {
            outline: none;
            border-color: #26772c;
            box-shadow: 0 0 0 4px rgba(38, 119, 44, .11);
        }
    </style>
</head>
<body class="min-h-screen bg-[#fbfcfb] text-[#101827]">
    <nav class="border-b border-gray-200 bg-white/95 px-5 py-5">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-sm font-semibold text-gray-600 transition-colors hover:text-[#1f6f29]">
                <svg class="h-5 w-5 text-[#1f6f29]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali ke Beranda
            </a>
            <span class="text-2xl font-extrabold tracking-tight text-[#14651f]">NexaSpace</span>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-5 py-10 lg:py-14">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-[470px_1fr] lg:gap-20">
            <aside class="lg:sticky lg:top-10 lg:self-start">
                @if($planData['highlight'])
                    <div class="mb-7 inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.16em] text-amber-600">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 1.75l2.28 4.62 5.1.74-3.69 3.59.87 5.08L10 13.38l-4.56 2.4.87-5.08-3.69-3.59 5.1-.74L10 1.75z"/>
                        </svg>
                        Paling Laris
                    </div>
                @endif

                <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-[#176523]">Paket yang Dipilih</p>
                <h1 class="mt-5 text-5xl font-black tracking-tight text-[#206f29]">{{ $planData['label'] }}</h1>

                <div class="mt-5 flex flex-wrap items-end gap-3">
                    <span class="text-4xl font-black tracking-tight text-[#101827] sm:text-5xl">{{ $planData['price'] }}</span>
                    <span class="pb-2 text-base font-semibold text-gray-600">/ {{ $planData['period'] }}</span>
                </div>

                <div class="mt-7 flex items-center gap-3 rounded-lg bg-gradient-to-r from-[#edf8f0] to-[#f8fbf8] px-4 py-4 text-[#176523]">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                    </svg>
                    <span class="text-sm font-extrabold">{{ $planData['quota'] }}</span>
                </div>

                <div class="my-9 h-px bg-gray-200"></div>

                <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-[#176523]">Yang Anda Dapatkan</p>
                <ul class="mt-6 space-y-4">
                    @foreach($planData['features'] as $feature)
                        <li class="flex items-start gap-4 text-base font-medium text-gray-700">
                            <span class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#26772c]">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>

                <div class="mt-9 flex gap-4 rounded-lg bg-[#edf8f0] px-5 py-5 text-[#176523]">
                    <svg class="mt-0.5 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15.75 9.75M12 3.75l7.5 3v5.25c0 4.125-2.7 7.875-7.5 9-4.8-1.125-7.5-4.875-7.5-9V6.75l7.5-3z"/>
                    </svg>
                    <p class="text-sm font-medium leading-6">
                        Semua paket sudah termasuk onboarding, migrasi data, dan dukungan WhatsApp. Tidak ada biaya tersembunyi.
                    </p>
                </div>

                <div class="my-9 h-px bg-gray-200"></div>

                <p class="text-sm font-medium text-gray-600">Ganti paket:</p>
                <div class="mt-4 grid grid-cols-3 gap-3">
                    @foreach(['lite' => 'LITE', 'pro' => 'PRO', 'custom' => 'CUSTOM'] as $key => $label)
                        <a href="{{ route('daftar', $key) }}"
                           class="rounded-lg border px-4 py-3 text-center text-sm font-extrabold transition
                                  {{ $selectedPlan === $key
                                      ? 'border-[#1f6f29] bg-gradient-to-r from-[#1d6d27] to-[#2f842e] text-white shadow-lg shadow-green-900/15'
                                      : 'border-gray-300 bg-white text-gray-700 hover:border-[#1f6f29] hover:text-[#1f6f29]' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </aside>

            <section class="pt-0 lg:pt-2">
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-black tracking-tight text-[#101827] sm:text-4xl">Lengkapi Data Pendaftaran</h2>
                    <p class="mt-4 text-base text-gray-600">
                        @if($selectedPlan === 'custom')
                            Isi form berikut dan tim kami akan menghubungi Anda via WhatsApp.
                        @else
                            Isi form berikut, lalu lanjutkan ke instruksi transfer bulan pertama.
                        @endif
                    </p>

                    <div id="alert-error" class="mt-6 hidden rounded-xl border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-700">Terjadi kesalahan. Periksa kembali isian Anda.</p>
                    </div>

                    <form id="reg-form" class="mt-8" novalidate>
                        @csrf

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-extrabold text-gray-800">Nama Anda <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" placeholder="Contoh: Pak Budi" class="form-field">
                                <p class="field-error" id="err-name"></p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-extrabold text-gray-800">Nama Kos <span class="text-red-500">*</span></label>
                                <input type="text" name="kos_name" id="kos_name" placeholder="Contoh: Kos Mutiara" class="form-field">
                                <p class="field-error" id="err-kos_name"></p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-extrabold text-gray-800">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" placeholder="email@anda.com" class="form-field">
                                <p class="field-error" id="err-email"></p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-extrabold text-gray-800">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" id="phone" placeholder="08xxxxxxxxxx" class="form-field">
                                <p class="field-error" id="err-phone"></p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="mb-2 block text-sm font-extrabold text-gray-800">Jumlah Kamar <span class="text-red-500">*</span></label>
                            <input type="number" name="room_count" id="room_count" placeholder="Contoh: 20" min="1" max="999" class="form-field">
                            <p class="field-error" id="err-room_count"></p>
                        </div>

                        <div class="mt-6">
                            <label class="mb-2 block text-sm font-extrabold text-gray-800">
                                Pesan / Catatan <span class="font-medium text-gray-500">(opsional)</span>
                            </label>
                            <textarea name="message" id="message" rows="5" placeholder="Tuliskan pertanyaan atau kebutuhan khusus Anda..." class="form-field resize-y"></textarea>
                            <p class="field-error" id="err-message"></p>
                        </div>

                        <input type="hidden" name="plan" value="{{ $selectedPlan }}">

                        <div class="mt-9">
                            <button type="submit" id="submit-btn"
                                    class="w-full rounded-lg bg-gradient-to-r from-[#1d6d27] to-[#2f842e] px-5 py-4 text-base font-extrabold text-white shadow-xl shadow-green-900/15 transition hover:from-[#15571e] hover:to-[#216b25] disabled:cursor-not-allowed disabled:opacity-60">
                                @if($selectedPlan === 'custom')
                                    <span id="btn-text">Hubungi Tim NexaSpace via WhatsApp</span>
                                    <span id="btn-loading" class="hidden">Memproses...</span>
                                @else
                                    <span id="btn-text">Daftar & Bayar Sekarang</span>
                                    <span id="btn-loading" class="hidden">Menyiapkan pembayaran...</span>
                                @endif
                            </button>

                            <p class="mt-5 flex items-center justify-center gap-2 text-center text-sm font-medium text-gray-500">
                                <svg class="h-5 w-5 text-[#1f6f29]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
                </div>
            </section>
        </div>
    </main>

    <footer class="mt-8 bg-gradient-to-r from-[#145c20] via-[#1d732b] to-[#0d4f1a] px-5 py-8 text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-xl bg-white/12">
                <svg class="h-9 w-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15.75 9.75M12 3.75l7.5 3v5.25c0 4.125-2.7 7.875-7.5 9-4.8-1.125-7.5-4.875-7.5-9V6.75l7.5-3z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-extrabold">Aman & Terpercaya</p>
                <p class="mt-1 max-w-3xl text-sm leading-6 text-white/80">
                    Bergabung dengan pemilik kos yang mempercayakan manajemen kosnya bersama NexaSpace.
                </p>
            </div>
        </div>
    </footer>

    <script>
        const PLAN = '{{ $selectedPlan }}';
        const REGISTER_URL = '{{ route("registration.store") }}';

        function clearErrors() {
            document.querySelectorAll('.field-error').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
            document.getElementById('alert-error').classList.add('hidden');
        }

        function showFieldError(field, msg) {
            const el = document.getElementById('err-' + field);
            const input = document.getElementById(field) || document.querySelector('[name="' + field + '"]');
            if (el) {
                el.textContent = msg;
                el.style.display = 'block';
            }
            if (input) {
                input.classList.add('input-error');
            }
        }

        function setLoading(loading) {
            const btn = document.getElementById('submit-btn');
            const text = document.getElementById('btn-text');
            const load = document.getElementById('btn-loading');
            btn.disabled = loading;
            text.classList.toggle('hidden', loading);
            load.classList.toggle('hidden', !loading);
        }

        document.getElementById('reg-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();
            setLoading(true);

            const formData = new FormData(this);
            const body = Object.fromEntries(formData.entries());

            try {
                const res = await fetch(REGISTER_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(body),
                });

                const data = await res.json();

                if (res.status === 422 && data.errors) {
                    setLoading(false);
                    for (const [field, messages] of Object.entries(data.errors)) {
                        showFieldError(field, messages[0]);
                    }
                    return;
                }

                if (!res.ok) {
                    throw new Error('Server error');
                }

                if (PLAN === 'custom' || data.wa_url) {
                    window.location.href = data.wa_url;
                    return;
                }

                if (data.payment_url) {
                    window.location.href = data.payment_url;
                } else {
                    throw new Error('No payment URL received');
                }
            } catch (err) {
                setLoading(false);
                document.getElementById('alert-error').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
