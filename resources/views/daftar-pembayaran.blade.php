<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Paket {{ strtoupper($registration->plan) }} - NexaSpace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; }
        .brand-bg { background-color: #0b7a27; }
        .soft-shadow { box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08); }
        .bank-card { box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06); }
    </style>
</head>
<body class="min-h-screen bg-[#f7f8f6] text-[#07111f]">
    <nav class="bg-white/95 border-b border-gray-200 px-5 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('daftar', $registration->plan) }}" class="flex items-center gap-3 text-gray-700 hover:text-[#0b7a27] transition-colors text-sm font-semibold">
                <svg class="w-5 h-5 text-[#0b7a27]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali ke Form
            </a>
            <span class="text-[#0b7a27] font-extrabold text-xl tracking-tight">NexaSpace</span>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-5 py-8 lg:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_390px] gap-8 items-start">
            <section class="space-y-5">
                <div class="bg-white border border-gray-200 rounded-2xl soft-shadow p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_210px] gap-6 items-center">
                        <div>
                            <p class="inline-flex items-center rounded-xl bg-[#eaf5ee] px-4 py-2 text-xs font-extrabold uppercase tracking-[0.22em] text-[#0a5d22]">
                                Pembayaran Manual
                            </p>
                            <h1 class="mt-5 max-w-2xl text-3xl sm:text-4xl font-extrabold leading-tight text-[#07111f]">
                                Transfer bulan pertama untuk mengaktifkan akun
                            </h1>
                            <p class="mt-4 max-w-2xl text-sm sm:text-base leading-7 text-gray-600">
                                Pilih salah satu rekening berikut, lakukan transfer sesuai nominal, lalu konfirmasi melalui WhatsApp.
                                Tim NexaSpace akan memverifikasi pembayaran sebelum akun juragan dan akun anak kos dibuat.
                            </p>
                        </div>

                        <div class="hidden md:flex justify-end">
                            <div class="relative h-36 w-36 rounded-full bg-[#eef7f1] flex items-center justify-center">
                                <span class="absolute -left-3 top-16 text-[#72b982]">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2l1.8 5.4L17 9.2l-5.2 1.8L10 16l-1.8-5L3 9.2l5.2-1.8L10 2z"/></svg>
                                </span>
                                <span class="absolute right-2 top-4 text-[#72b982]">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2l1.5 4.5L16 8l-4.5 1.5L10 14l-1.5-4.5L4 8l4.5-1.5L10 2z"/></svg>
                                </span>
                                <div class="h-20 w-28 rounded-xl bg-gradient-to-br from-[#147b2f] to-[#8fd39d] p-3 shadow-xl">
                                    <div class="h-2 w-14 rounded-full bg-white/80"></div>
                                    <div class="mt-8 flex gap-2">
                                        <div class="h-2 w-7 rounded-full bg-white/80"></div>
                                        <div class="h-2 w-10 rounded-full bg-white/60"></div>
                                    </div>
                                </div>
                                <div class="absolute -bottom-1 right-1 h-14 w-14 rounded-full bg-[#65bd69] border-4 border-white flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach ($banks as $bank)
                        <div class="bank-card bg-white border border-gray-200 rounded-2xl p-5">
                            <div class="flex items-start gap-4">
                                <div class="h-16 w-20 rounded-xl border border-gray-200 bg-white flex items-center justify-center flex-shrink-0">
                                    <span class="{{ $bank['logo_class'] }} text-xl font-black tracking-tight">{{ $bank['logo'] }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-base font-extrabold leading-6 text-[#07111f]">{{ $bank['name'] }}</p>
                                    <p class="mt-4 text-xs font-extrabold uppercase tracking-[0.18em] text-[#0a5d22]">Nomor Rekening</p>
                                    <p class="mt-1 text-2xl font-extrabold tracking-wide text-[#07111f] break-all">{{ $bank['account_number'] }}</p>
                                </div>
                            </div>

                            <div class="mt-5 border-t border-dashed border-gray-200 pt-4 flex items-center gap-3">
                                <svg class="w-6 h-6 text-[#0b7a27] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0"/>
                                </svg>
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#0a5d22]">Atas Nama</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $bank['account_name'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <aside class="lg:sticky lg:top-8">
                <div class="bg-white border border-gray-200 rounded-2xl soft-shadow overflow-hidden">
                    <div class="relative overflow-hidden bg-gradient-to-br from-[#087728] via-[#128936] to-[#0c5d21] px-7 py-8">
                        <div class="absolute -bottom-16 right-4 h-36 w-48 rotate-[-35deg] rounded-3xl bg-white/10"></div>
                        <p class="relative text-xs font-extrabold uppercase tracking-[0.22em] text-green-100">Ringkasan</p>
                        <h2 class="relative mt-3 text-4xl font-extrabold text-white drop-shadow-sm">{{ $planData['label'] }}</h2>
                    </div>

                    <div class="p-7 space-y-6">
                        <div class="flex items-start gap-4 border-b border-gray-200 pb-5">
                            <div class="h-12 w-12 rounded-xl bg-[#edf8f0] flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-[#0b7a27]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H6.75A2.25 2.25 0 004.5 4.5v15A2.25 2.25 0 006.75 21.75h10.5A2.25 2.25 0 0019.5 19.5v-5.25z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25h6M9 17.25h3"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.18em] text-gray-500 font-bold">ID Pendaftaran</p>
                                <p class="mt-1 text-lg font-extrabold text-[#07111f]">REG-{{ $registration->id }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 rounded-xl bg-[#edf8f0] flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-[#0b7a27]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.18em] text-gray-500 font-bold">Nama Kos</p>
                                <p class="mt-1 text-lg font-extrabold text-[#07111f]">{{ $registration->kos_name }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[#bfe7cc] bg-gradient-to-br from-[#f1fbf4] to-white p-5">
                            <p class="text-xs uppercase tracking-[0.18em] text-gray-600 font-bold">Nominal Transfer</p>
                            <p class="mt-3 text-4xl font-black tracking-tight text-[#0b7a27]">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                        </div>

                        <div class="rounded-2xl border border-[#c7ead2] bg-[#f6fbf7] p-5 flex gap-4">
                            <svg class="w-9 h-9 text-[#0b7a27] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 12a8.25 8.25 0 11-15.218-4.452A8.25 8.25 0 0120.25 12z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 20.25L5.25 21l.75-2.25"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75c.75 2.25 2.25 3.75 4.5 4.5"/>
                            </svg>
                            <p class="text-sm leading-6 text-gray-700">
                                Setelah transfer, tekan tombol konfirmasi. WhatsApp akan terbuka dengan detail pendaftaran otomatis.
                            </p>
                        </div>

                        <a href="{{ $confirmationUrl }}"
                           target="_blank"
                           rel="noopener"
                           class="flex w-full items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-[#0b7a27] to-[#0fa13a] px-4 py-4 text-center text-base font-extrabold text-white shadow-xl shadow-green-900/20 transition hover:from-[#08651f] hover:to-[#0b8d30]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 12a8.25 8.25 0 11-15.218-4.452A8.25 8.25 0 0120.25 12z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 20.25L5.25 21l.75-2.25"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75c.75 2.25 2.25 3.75 4.5 4.5"/>
                            </svg>
                            Konfirmasi via WhatsApp
                        </a>

                        <div class="flex items-center gap-4">
                            <div class="h-px flex-1 bg-gray-200"></div>
                            <span class="text-sm text-gray-400">atau</span>
                            <div class="h-px flex-1 bg-gray-200"></div>
                        </div>

                        <a href="{{ route('home') }}" class="flex items-center justify-center gap-3 text-sm font-extrabold text-[#0b7a27] hover:text-[#07551c]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
