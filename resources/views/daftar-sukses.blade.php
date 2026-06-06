<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil — NexaSpace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', 'Segoe UI', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-10 text-center">

        {{-- Check icon --}}
        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-[#306D29]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
        </div>

        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Pembayaran Berhasil!</h1>
        <p class="text-gray-500 text-sm leading-relaxed mb-6">
            Pendaftaran Anda sudah diterima dan sedang dalam review.<br>
            Tim NexaSpace akan menghubungi Anda dalam <strong>1×24 jam</strong> melalui WhatsApp atau email yang Anda daftarkan.
        </p>

        <div class="bg-[#F0FBF0] border border-[#C6E8C6] rounded-xl px-5 py-4 mb-8 text-left">
            <p class="text-xs font-bold text-[#306D29] uppercase tracking-widest mb-2">Langkah Selanjutnya</p>
            <ol class="text-sm text-gray-700 space-y-1.5 list-decimal list-inside">
                <li>Tim kami memverifikasi pembayaran Anda</li>
                <li>Akun juragan + anak kos disiapkan sesuai paket</li>
                <li>Kredensial dikirim ke email yang Anda daftarkan</li>
                <li>Anda langsung bisa login dan mulai pakai NexaSpace</li>
            </ol>
        </div>

        <a href="{{ route('home') }}"
           class="inline-block w-full py-3 rounded-xl font-bold text-sm text-white bg-[#306D29]
                  hover:bg-[#0D530E] transition-colors shadow cursor-pointer">
            Kembali ke Beranda
        </a>

        <p class="text-xs text-gray-400 mt-4">
            Ada pertanyaan?
            <a href="https://wa.me/6285249678700" target="_blank" class="text-[#306D29] hover:underline">
                Hubungi kami via WhatsApp
            </a>
        </p>
    </div>
</body>
</html>
