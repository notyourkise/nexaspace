<!DOCTYPE html>
<html lang="id" style="scroll-behavior:smooth;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NexaSpace — Manajemen Kos Modern</title>
    <meta name="description" content="Otomatiskan penagihan, blokir WiFi otomatis, dan kelola kos dari satu layar. Biar sistem yang kerja, juragan duduk manis.">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nexa.png') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="antialiased text-gray-900 bg-white">

{{-- ═══════════════════════════════════════════════════════════
     A. FLOATING GLASSMORPHISM NAVBAR
════════════════════════════════════════════════════════════ --}}
<nav class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-5xl">
    <div class="backdrop-blur-lg bg-white/30 border border-white/30 shadow-xl rounded-full
                px-6 py-3 flex items-center justify-between gap-4">

        <a href="/" class="flex-shrink-0">
            <img src="{{ asset('images/nexaspace.webp') }}"
                 alt="NexaSpace"
                 class="h-12 md:h-14 w-auto object-contain">
        </a>

        {{-- Middle nav links (desktop) --}}
        <div class="hidden md:flex items-center gap-1 flex-1 justify-center">
            <a href="/"
               class="px-4 py-2 rounded-full text-sm font-bold text-gray-900
                      hover:text-[#306D29] hover:bg-[#306D29]/10 transition-all duration-150">
                Home
            </a>
            <a href="#fitur"
               class="px-4 py-2 rounded-full text-sm font-bold text-gray-900
                      hover:text-[#306D29] hover:bg-[#306D29]/10 transition-all duration-150">
                Fitur
            </a>
            <a href="#harga"
               class="px-4 py-2 rounded-full text-sm font-bold text-gray-900
                      hover:text-[#306D29] hover:bg-[#306D29]/10 transition-all duration-150">
                Harga
            </a>
            <a href="#testimoni"
               class="px-4 py-2 rounded-full text-sm font-bold text-gray-900
                      hover:text-[#306D29] hover:bg-[#306D29]/10 transition-all duration-150">
                Testimoni
            </a>
            <a href="#footer"
               class="px-4 py-2 rounded-full text-sm font-bold text-gray-900
                      hover:text-[#306D29] hover:bg-[#306D29]/10 transition-all duration-150">
                Kontak
            </a>
        </div>

        {{-- Right buttons --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="/tenant/login"
               class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                      bg-[#306D29] text-white hover:bg-[#0D530E]
                      transition-all duration-150 shadow-sm whitespace-nowrap">
                Portal Anak Kos
            </a>
        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════════════════════
     B. HERO SECTION
════════════════════════════════════════════════════════════ --}}
<section class="bg-[#FBF5DD] pt-36 pb-24 lg:pt-44 lg:pb-32">
    <div class="max-w-4xl mx-auto px-6 text-center">

        {{-- Copy --}}
        <div>

            <span class="inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase
                         px-3 py-1.5 rounded-full bg-[#306D29]/10 text-[#306D29] border border-[#306D29]/20 mb-6">
                ✦ Platform Kos No.1 Indonesia
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-[3.25rem] font-extrabold leading-tight tracking-tight text-gray-900 mb-6">
                Urus Kos Nggak Perlu Pusing.<br>
                <span class="text-[#306D29]">Biar Sistem yang Kerja,<br> Juragan Tinggal Duduk Manis.</span>
            </h1>

            <p class="text-lg text-gray-600 leading-relaxed max-w-2xl mx-auto mb-10">
                Tinggalkan rekap Excel manual dan drama tagihan telat.
                NexaSpace otomatiskan pengingat bayar, blokir WiFi pintar,
                dan bereskan pembukuan kos Anda dalam satu layar.
            </p>

            {{-- CTAs --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-10">
                <a href="https://wa.me/6285249678700" target="_blank"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5
                          px-8 py-4 rounded-2xl font-bold text-base text-white bg-[#306D29]
                          hover:bg-[#0D530E] transition-colors shadow-lg shadow-[#306D29]/25">
                    Hubungi Kami
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
                <a href="#harga"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                          px-8 py-4 rounded-2xl font-semibold text-base text-[#306D29]
                          border-2 border-[#306D29] hover:bg-[#306D29] hover:text-white
                          transition-colors">
                    Lihat Paket Harga
                </a>
            </div>


        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     C. HOW IT WORKS
════════════════════════════════════════════════════════════ --}}
<section id="sebelum-sesudah" class="bg-white py-24">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-14">
            <span class="inline-block text-xs font-bold tracking-widest uppercase
                         px-3 py-1.5 rounded-full bg-[#FBF5DD] text-[#306D29] mb-4">
                Perbandingan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                Masih Kelola Kos Secara Manual?
            </h2>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                Lihat perbedaan nyata antara cara lama yang melelahkan dengan NexaSpace.
            </p>
        </div>

        {{-- Comparison table --}}
        <div class="relative flex flex-col md:flex-row gap-0">

            {{-- SEBELUM --}}
            <div class="flex-1 p-10">
                <p class="text-xs font-bold tracking-widest uppercase text-red-400 mb-1">Sebelum NexaSpace</p>
                <p class="text-xl font-extrabold text-gray-800 mb-8">Cara Lama yang Melelahkan</p>
                <ul class="flex flex-col gap-4">
                    @foreach([
                        'Rekap tagihan pakai Excel, sering typo & error',
                        'Kejar penyewa satu-satu via WhatsApp tiap bulan',
                        'WiFi tetap nyala meski penyewa belum bayar',
                        'Konfirmasi bayar manual, foto struk tidak jelas',
                        'Tidak tahu siapa yang lunas atau masih nunggak',
                        'Penyewa harus WA dulu untuk cek tagihan mereka',
                    ] as $pain)
                        <li class="flex items-start gap-3">
                            <span class="text-sm text-gray-500 leading-relaxed">{{ $pain }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Vertical divider (desktop) --}}
            <div class="hidden md:block w-px bg-gray-200 self-stretch mx-0"></div>
            {{-- Horizontal divider (mobile) --}}
            <div class="md:hidden h-px bg-gray-200 mx-10"></div>

            {{-- SESUDAH --}}
            <div class="flex-1 p-10">
                <p class="text-xs font-bold tracking-widest uppercase text-[#306D29] mb-1">Sesudah NexaSpace</p>
                <p class="text-xl font-extrabold text-gray-800 mb-8">Tenang, Semua Berjalan Otomatis</p>
                <ul class="flex flex-col gap-4">
                    @foreach([
                        'Tagihan & laporan bulanan dibuat otomatis oleh sistem',
                        'Pengingat tagihan terkirim sendiri, tanpa juragan turun tangan',
                        'WiFi dibatasi otomatis saat telat, pulih langsung saat lunas',
                        'Penyewa bayar via Midtrans, status lunas tercatat real-time',
                        'Dashboard menampilkan status tiap kamar secara langsung',
                        'Portal mandiri, penyewa cek dan bayar tagihan sendiri 24/7',
                    ] as $benefit)
                        <li class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-[#306D29] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            <span class="text-sm text-gray-700 leading-relaxed font-medium">{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>

        {{-- Bottom CTA --}}
        <div class="text-center mt-32">
            <a href="https://wa.me/6285249678700"
               target="_blank"
               class="inline-flex items-center gap-2.5 px-8 py-4 rounded-2xl font-bold text-base text-white
                      transition-colors shadow-lg shadow-[#306D29]/25 bg-[#306D29] hover:bg-[#0D530E]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Konsultasi Gratis via WhatsApp
            </a>
            <p class="text-sm text-gray-400 mt-3">Balas dalam hitungan menit · Senin sampai Sabtu, 08.00 s.d. 21.00 WIB</p>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     D. FEATURES SECTION
════════════════════════════════════════════════════════════ --}}
<section id="fitur" class="bg-[#FBF5DD] py-24">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-14">
            <span class="inline-block text-xs font-bold tracking-widest uppercase
                         px-3 py-1.5 rounded-full bg-[#306D29] text-white mb-4">
                Fitur Unggulan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                Kenapa Memilih NexaSpace?
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Satu platform untuk semua kebutuhan manajemen kos, dari pencatatan
                hingga integrasi hardware MikroTik.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Feature 1 --}}
            <div class="group flex flex-col items-start p-8 rounded-2xl bg-white border-2 border-transparent
                        hover:border-[#306D29] transition-all duration-200 hover:shadow-lg">
                <div class="w-12 h-12 rounded-xl bg-[#FBF5DD] flex items-center justify-center mb-5
                            group-hover:bg-[#306D29] transition-colors duration-200">
                    <svg class="w-6 h-6 text-[#306D29] group-hover:text-white transition-colors duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Manajemen Terpusat</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Semua kamar, penyewa, dan tagihan dalam satu dasbor.
                    Data real-time, laporan bulanan otomatis, tanpa Excel.
                </p>
            </div>

            {{-- Feature 2 --}}
            <div class="group flex flex-col items-start p-8 rounded-2xl bg-white border-2 border-transparent
                        hover:border-[#306D29] transition-all duration-200 hover:shadow-lg">
                <div class="w-12 h-12 rounded-xl bg-[#FBF5DD] flex items-center justify-center mb-5
                            group-hover:bg-[#306D29] transition-colors duration-200">
                    <svg class="w-6 h-6 text-[#306D29] group-hover:text-white transition-colors duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Pembayaran Otomatis</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Integrasi Midtrans Snap, penyewa bayar via transfer, QRIS, atau kartu.
                    Status lunas langsung tercatat, nol konfirmasi manual.
                </p>
            </div>

            {{-- Feature 3 --}}
            <div class="group flex flex-col items-start p-8 rounded-2xl bg-white border-2 border-transparent
                        hover:border-[#306D29] transition-all duration-200 hover:shadow-lg">
                <div class="w-12 h-12 rounded-xl bg-[#FBF5DD] flex items-center justify-center mb-5
                            group-hover:bg-[#306D29] transition-colors duration-200">
                    <svg class="w-6 h-6 text-[#306D29] group-hover:text-white transition-colors duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Smart WiFi Auto-Throttle</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    WiFi penyewa telat bayar dibatasi otomatis. Pulih sendiri saat lunas.
                    Terintegrasi langsung ke router MikroTik.
                </p>
            </div>

        </div>

        {{-- Secondary pills --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">
            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white">
                <div class="w-11 h-11 flex-shrink-0 rounded-xl bg-[#306D29] flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-1">Portal Penyewa Mandiri</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Penyewa cek tagihan, bayar, dan unggah bukti sendiri. Tersedia 24/7 tanpa perlu hubungi admin.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white">
                <div class="w-11 h-11 flex-shrink-0 rounded-xl bg-[#306D29] flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-1">Aman & Terverifikasi</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Webhook Midtrans verifikasi SHA512. Akses panel admin dan tenant terpisah sepenuhnya.</p>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     E. PRICING SECTION
════════════════════════════════════════════════════════════ --}}
<section id="harga" class="bg-[#E7E1B1] py-24">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-14">
            <span class="inline-block text-xs font-bold tracking-widest uppercase
                         px-3 py-1.5 rounded-full bg-[#306D29] text-white mb-4">
                Harga Transparan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                Pilih Paket Sesuai Skala Bisnis Anda
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:items-start">

            {{-- LITE --}}
            <div class="flex flex-col bg-white rounded-2xl p-8 shadow-md border border-gray-200">
                <p class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-4">LITE</p>
                <p class="text-4xl font-extrabold text-gray-900 mb-1">Rp 199.000</p>
                <p class="text-sm text-gray-400 mb-4">per bulan</p>
                <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-6">
                    <svg class="w-4 h-4 text-[#306D29] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                    </svg>
                    Maksimal <strong>20 Kamar</strong>
                </div>
                <ul class="flex flex-col gap-3.5 mb-8 flex-1">
                    @foreach(['Pencatatan Terpusat','Portal Penyewa Mandiri','Tanpa Integrasi Alat'] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="w-5 h-5 rounded-full bg-[#FBF5DD] flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-[#306D29]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            {{ $feat }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('daftar', 'lite') }}"
                   class="block w-full text-center py-3 rounded-xl font-semibold text-sm
                          border-2 border-[#306D29] text-[#306D29]
                          hover:bg-[#306D29] hover:text-white transition-colors duration-150">
                    Pilih LITE
                </a>
            </div>

            {{-- PRO (Highlighted, lifted) --}}
            <div class="relative flex flex-col bg-white rounded-2xl p-8 shadow-2xl border-2 border-[#306D29]
                        md:-mt-5 md:-mb-5 md:py-11">

                <div class="absolute -top-4 left-0 right-0 flex justify-center">
                    <span class="inline-flex items-center gap-1.5 px-5 py-1.5 rounded-full
                                 text-xs font-extrabold tracking-widest uppercase
                                 bg-[#306D29] text-white shadow-md">
                        ⭐ Paling Laris
                    </span>
                </div>

                <p class="text-xs font-bold tracking-widest uppercase text-[#306D29] mb-4 mt-2">PRO</p>
                <p class="text-4xl font-extrabold text-gray-900 mb-1">Rp 499.000</p>
                <p class="text-sm text-gray-400 mb-4">per bulan</p>
                <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                    <svg class="w-4 h-4 text-[#306D29] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                    </svg>
                    Maksimal <strong>40 Kamar</strong>
                </div>

                {{-- Promo row --}}
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#FBF5DD] border border-[#E7E1B1] mb-6">
                    <p class="flex-1 text-xs text-gray-400 line-through leading-snug min-w-0">
                        Biaya Instalasi &amp; Alat Rp&nbsp;1.500.000
                    </p>
                    <span class="flex-shrink-0 px-2.5 py-1 rounded-lg text-xs font-extrabold
                                 bg-[#306D29] text-white shadow-sm whitespace-nowrap">
                        ✓ GRATIS Instalasi
                    </span>
                </div>

                <p class="text-xs font-semibold text-gray-400 mb-3">
                    ✦ Semua fitur LITE, ditambah:
                </p>
                <ul class="flex flex-col gap-3.5 mb-8 flex-1">
                    @foreach(['Tagihan Otomatis Midtrans','Smart WiFi Auto-Block','Gratis Peminjaman MikroTik & Switch'] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-700 font-medium">
                            <span class="w-5 h-5 rounded-full bg-[#306D29] flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            {{ $feat }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('daftar', 'pro') }}"
                   class="block w-full text-center py-3.5 rounded-xl font-bold text-sm text-white
                          bg-[#306D29] hover:bg-[#0D530E] transition-colors shadow-lg">
                    Mulai Paket PRO →
                </a>
            </div>

            {{-- CUSTOM --}}
            <div class="flex flex-col bg-white rounded-2xl p-8 shadow-md border border-gray-200">
                <p class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-4">CUSTOM</p>
                <p class="text-3xl font-extrabold text-gray-900 mb-1">Hubungi Kami</p>
                <p class="text-sm text-gray-400 mb-4">harga disesuaikan kebutuhan</p>
                <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-6">
                    <svg class="w-4 h-4 text-[#306D29] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                    </svg>
                    Skala Besar <strong>(50+ Kamar)</strong>
                </div>
                <p class="text-xs font-semibold text-gray-400 mb-3">
                    ✦ Semua fitur PRO, ditambah:
                </p>
                <ul class="flex flex-col gap-3.5 mb-8 flex-1">
                    @foreach(['Topologi Jaringan Khusus','Dukungan Teknis Prioritas','SLA & Kontrak Khusus'] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="w-5 h-5 rounded-full bg-[#FBF5DD] flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-[#306D29]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            {{ $feat }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('daftar', 'custom') }}"
                   class="block w-full text-center py-3 rounded-xl font-semibold text-sm
                          border-2 border-gray-300 text-gray-600
                          hover:border-[#306D29] hover:text-[#306D29] transition-colors duration-150">
                    Hubungi Layanan
                </a>
            </div>

        </div>

        <p class="text-center text-sm text-gray-500 mt-10">
            Semua paket sudah termasuk onboarding, migrasi data, dan dukungan WhatsApp.
            Tidak ada biaya tersembunyi.
        </p>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     F. PERBANDINGAN PAKET
════════════════════════════════════════════════════════════ --}}
<section id="perbandingan" class="bg-[#F8FAF8] py-20 px-4">
    <div class="max-w-5xl mx-auto">

        <div class="text-center mb-12">
            <span class="inline-block text-xs font-bold tracking-widest uppercase
                         px-3 py-1.5 rounded-full bg-[#306D29]/10 text-[#306D29] mb-4">
                Perbandingan Paket
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-3">
                Pilih Paket yang Tepat
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm leading-relaxed">
                Semua paket sudah termasuk onboarding, migrasi data awal, dan dukungan WhatsApp.
            </p>
        </div>

        {{-- Comparison table --}}
        <div class="overflow-x-auto rounded-2xl shadow-sm border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="bg-white text-left px-6 py-4 text-gray-500 font-semibold w-2/5 border-b border-gray-100">
                            Fitur
                        </th>
                        <th class="bg-white text-center px-6 py-4 text-gray-700 font-bold border-b border-gray-100">
                            LITE
                            <div class="text-xs font-normal text-gray-400 mt-0.5">Rp 199rb/bln</div>
                        </th>
                        <th class="bg-[#306D29] text-center px-6 py-4 text-white font-bold border-b border-[#2a5e24] relative">
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 text-[10px] font-extrabold uppercase tracking-wider px-3 py-0.5 rounded-full shadow">
                                ⭐ Paling Laris
                            </span>
                            PRO
                            <div class="text-xs font-normal text-green-200 mt-0.5">Rp 499rb/bln</div>
                        </th>
                        <th class="bg-white text-center px-6 py-4 text-gray-700 font-bold border-b border-gray-100">
                            CUSTOM
                            <div class="text-xs font-normal text-gray-400 mt-0.5">Hubungi Kami</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $rows = [
                        ['Kuota Kamar',                   'Hingga 20 kamar',   'Hingga 40 kamar',  '50+ kamar'],
                        ['Auto-throttle WiFi Otomatis',   'check', 'check', 'check'],
                        ['Portal Anak Kos (bayar online)', 'check', 'check', 'check'],
                        ['Pencatatan Tagihan Terpusat',   'check', 'check', 'check'],
                        ['Notifikasi WhatsApp Real-time', 'check', 'check', 'check'],
                        ['Peminjaman Perangkat MikroTik', 'dash',  'check', 'check'],
                        ['Peminjaman Switch Jaringan',    'dash',  'check', 'check'],
                        ['Gratis Instalasi Jaringan',     'dash',  'check', 'check'],
                        ['Topologi Jaringan Khusus',      'dash',  'dash',  'check'],
                        ['Dukungan Teknis Prioritas 24/7','dash',  'dash',  'check'],
                        ['SLA & Kontrak Khusus',          'dash',  'dash',  'check'],
                    ];
                    @endphp

                    @foreach($rows as $i => $row)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                        <td class="px-6 py-3.5 text-gray-700 font-medium">{{ $row[0] }}</td>

                        @foreach([$row[1], $row[2], $row[3]] as $colIdx => $val)
                        <td class="text-center px-6 py-3.5 {{ $colIdx === 1 ? 'bg-[#306D29]/5' : '' }}">
                            @if($val === 'check')
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full
                                             {{ $colIdx === 1 ? 'bg-[#306D29] text-white' : 'bg-[#306D29]/15 text-[#306D29]' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                </span>
                            @elseif($val === 'dash')
                                <span class="text-gray-300 text-lg font-light">—</span>
                            @else
                                <span class="text-xs {{ $colIdx === 1 ? 'text-[#306D29] font-semibold' : 'text-gray-600' }}">{{ $val }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-5 bg-white"></td>
                        <td class="px-6 py-5 text-center bg-white">
                            <a href="{{ route('daftar', 'lite') }}"
                               class="inline-block px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#306D29]
                                      text-[#306D29] hover:bg-[#306D29] hover:text-white transition-all">
                                Pilih LITE
                            </a>
                        </td>
                        <td class="px-6 py-5 text-center bg-[#306D29]/5">
                            <a href="{{ route('daftar', 'pro') }}"
                               class="inline-block px-5 py-2.5 rounded-xl text-xs font-bold bg-[#306D29]
                                      text-white hover:bg-[#0D530E] transition-all shadow-lg shadow-[#306D29]/25">
                                Pilih PRO
                            </a>
                        </td>
                        <td class="px-6 py-5 text-center bg-white">
                            <a href="{{ route('daftar', 'custom') }}"
                               class="inline-block px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-gray-300
                                      text-gray-600 hover:border-[#306D29] hover:text-[#306D29] transition-all">
                                Hubungi Kami
                            </a>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     G. TESTIMONIALS (auto-scroll marquee)
════════════════════════════════════════════════════════════ --}}

{{-- Keyframe animation for the infinite horizontal scroll --}}
<style>
    @keyframes marquee {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .animate-marquee { animation: marquee 90s linear infinite; }
    .marquee-track:hover .animate-marquee { animation-play-state: paused; }
</style>

<section id="testimoni" class="bg-white py-24 overflow-hidden">
    <div class="max-w-6xl mx-auto px-6 mb-14 text-center">
        <span class="inline-block text-xs font-bold tracking-widest uppercase
                     px-3 py-1.5 rounded-full bg-[#FBF5DD] text-[#306D29] mb-4">
            Testimoni
        </span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
            Apa Kata Juragan Kos Kami?
        </h2>
        <p class="text-lg text-gray-500 max-w-xl mx-auto">
            Dipercaya oleh puluhan pemilik kos di seluruh Indonesia.
        </p>
    </div>

    @php
    $testimonials = [
        [
            'name'    => 'Pak Budi Santoso',
            'role'    => 'Pemilik Kos 24 Kamar · Bandung',
            'text'    => 'Dulu tiap bulan pusing nagih penyewa satu-satu. Sekarang WiFi-nya sendiri yang "nagih". Penyewa yang belum bayar langsung tersumbat. Ngerasa punya asisten pribadi!',
            'rating'  => 5,
            'initial' => 'B',
            'color'   => '#3b82f6',
        ],
        [
            'name'    => 'Bu Ratna Dewi',
            'role'    => 'Juragan Kos 12 Kamar · Yogyakarta',
            'text'    => 'Sebelum NexaSpace, pembukuan saya masih di Excel dan sering berantakan. Sekarang semua tercatat rapi, laporan bulanan keluar otomatis. Rekomendasiin banget!',
            'rating'  => 5,
            'initial' => 'R',
            'color'   => '#ef4444',
        ],
        [
            'name'    => 'Mas Hendri W.',
            'role'    => 'Investor Kos 38 Kamar · Surabaya',
            'text'    => 'Yang bikin saya terkesan itu fitur MikroTik-nya. Beneran kerja! WiFi langsung dibatasi otomatis, dan pas penyewa bayar, langsung normal lagi. Nggak perlu ngoprek router.',
            'rating'  => 5,
            'initial' => 'H',
            'color'   => '#10b981',
        ],
        [
            'name'    => 'Mbak Sinta P.',
            'role'    => 'Pemilik Kos 16 Kamar · Jakarta',
            'text'    => 'Portal penyewa mandirinya mantap! Penyewa saya bisa cek tagihan dan bayar sendiri via Midtrans. Saya jadi nggak perlu WA satu-satu lagi. Hemat waktu banget.',
            'rating'  => 5,
            'initial' => 'S',
            'color'   => '#8b5cf6',
        ],
        [
            'name'    => 'Pak Darmawan',
            'role'    => 'Pemilik Kos 30 Kamar · Semarang',
            'text'    => 'Awalnya skeptis, tapi setelah coba demo langsung percaya. Setup MikroTik dibantu tim NexaSpace sampai beres. Sekarang 30 kamar saya terpantau dari HP aja. Luar biasa!',
            'rating'  => 5,
            'initial' => 'D',
            'color'   => '#f59e0b',
        ],
        [
            'name'    => 'Bu Wulan K.',
            'role'    => 'Pengelola Kos 20 Kamar · Malang',
            'text'    => 'Penyewa saya kebanyakan mahasiswa yang suka telat bayar. Dengan NexaSpace, mereka jadi lebih disiplin sendiri karena tahu WiFi bakal dibatasi. Ampuh banget!',
            'rating'  => 5,
            'initial' => 'W',
            'color'   => '#14b8a6',
        ],
        [
            'name'    => 'Pak Agus Trianto',
            'role'    => 'Pemilik Kos 18 Kamar · Bekasi',
            'text'    => 'Tagihan otomatis via Midtrans itu game changer. Penyewa tinggal klik link, bayar QRIS, selesai. Tidak ada lagi foto struk yang tidak jelas di WhatsApp saya.',
            'rating'  => 5,
            'initial' => 'A',
            'color'   => '#f97316',
        ],
        [
            'name'    => 'Mbak Lestari N.',
            'role'    => 'Manajer Kos 45 Kamar · Depok',
            'text'    => 'Saya kelola kos milik 3 orang sekaligus, dulu repot banget. Sekarang cukup satu dashboard, semua terpantau. Tim NexaSpace juga responsif kalau ada pertanyaan.',
            'rating'  => 5,
            'initial' => 'L',
            'color'   => '#ec4899',
        ],
    ];

    @endphp

    {{-- Single row — kanan ke kiri, pelan (90s), pause saat hover --}}
    <div class="marquee-track">
        <div class="animate-marquee flex gap-6 w-max">
            @foreach(array_merge($testimonials, $testimonials) as $t)
                <div class="flex-shrink-0 w-80 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-1 mb-4">
                        @for($s = 0; $s < $t['rating']; $s++)
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed mb-5 line-clamp-3">
                        "{{ $t['text'] }}"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                             style="background-color: {{ $t['color'] }}">
                            {{ $t['initial'] }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 leading-tight">{{ $t['name'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $t['role'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</section>

{{-- Footer link underline-from-center animation --}}
<style>
    .footer-link {
        position: relative;
        display: inline-block;
    }
    .footer-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        right: 50%;
        height: 1px;
        background: #ffffff;
        transition: left 0.25s ease, right 0.25s ease;
    }
    .footer-link:hover::after {
        left: 0;
        right: 0;
    }
</style>

{{-- ═══════════════════════════════════════════════════════════
     G. FOOTER
════════════════════════════════════════════════════════════ --}}
<footer id="footer" class="bg-[#0D530E]">
    <div class="max-w-6xl mx-auto px-6 pt-16 pb-8">

        {{-- 4-column grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 mb-14">

            {{-- Kolom 1: Brand --}}
            <div>
                <a href="/" class="inline-block mb-5">
                    <img src="{{ asset('images/nexaspace.webp') }}"
                         alt="NexaSpace"
                         style="height:8.75rem;"
                         class="w-auto object-contain brightness-0 invert">
                </a>
                <p class="text-sm text-white/55 leading-relaxed mb-6">
                    Platform manajemen kos modern, tagihan otomatis, WiFi pintar, dan portal penyewa dalam satu sistem.
                </p>
                <div class="flex items-center gap-2">
                    <a href="https://wa.me/6285249678700" target="_blank"
                       class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    <a href="mailto:hello@nexaspace.site"
                       class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Kolom 2: Navigasi --}}
            <div>
                <p class="text-xs font-bold tracking-widest uppercase text-white/35 mb-5">Navigasi</p>
                <ul class="flex flex-col gap-3.5">
                    @foreach([
                        ['href' => '/',               'label' => 'Beranda'],
                        ['href' => '#fitur',           'label' => 'Fitur'],
                        ['href' => '#harga',           'label' => 'Harga'],
                        ['href' => '#testimoni',       'label' => 'Testimoni'],
                        ['href' => '#sebelum-sesudah', 'label' => 'Perbandingan'],
                    ] as $link)
                        <li>
                            <a href="{{ $link['href'] }}"
                               class="footer-link text-sm font-bold text-white">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Kolom 3: Akses Panel --}}
            <div>
                <p class="text-xs font-bold tracking-widest uppercase text-white/35 mb-5">Akses Panel</p>
                <ul class="flex flex-col gap-3.5">
                    <li>
                        <a href="/tenant/login" class="footer-link text-sm font-bold text-white">
                            Portal Anak Kos
                        </a>
                    </li>
                </ul>

                <p class="text-xs font-bold tracking-widest uppercase text-white/35 mt-8 mb-5">Jam Operasional</p>
                <p class="text-sm text-white/60 leading-relaxed">
                    Senin s.d. Sabtu<br>
                    08.00 s.d. 21.00 WIB
                </p>
            </div>

            {{-- Kolom 4: Kontak & Alamat --}}
            <div>
                <p class="text-xs font-bold tracking-widest uppercase text-white/35 mb-5">Hubungi Kami</p>
                <ul class="flex flex-col gap-3.5 mb-8">
                    <li>
                        <a href="https://wa.me/6285249678700" target="_blank"
                           class="footer-link text-sm font-bold text-white">
                            +62 852-4967-8700
                        </a>
                    </li>
                    <li>
                        <a href="mailto:hello@nexaspace.site"
                           class="footer-link text-sm font-bold text-white">
                            hello@nexaspace.site
                        </a>
                    </li>
                </ul>

                <p class="text-xs font-bold tracking-widest uppercase text-white/35 mb-5">Kantor</p>
                <address class="not-italic text-sm text-white/60 leading-relaxed">
                    Jl. Pemuda No. 47,<br>
                    Rawamangun, Pulo Gadung,<br>
                    Jakarta Timur 13220
                </address>
            </div>

        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-white/10 pt-6 text-center">
            <p class="text-xs font-bold text-white">© {{ date('Y') }} NexaSpace. Hak cipta dilindungi.</p>
        </div>

    </div>
</footer>

{{-- Modal form dihapus — registrasi sekarang di halaman /daftar/{plan} --}}
</body>
</html>
