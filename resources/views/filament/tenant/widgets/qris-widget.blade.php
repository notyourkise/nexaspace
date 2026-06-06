<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-qr-code" class="h-5 w-5 text-primary-500" />
                <span>Pembayaran via QRIS</span>
            </div>
        </x-slot>

        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            {{-- QR Code image --}}
            <div class="shrink-0">
                <img
                    src="{{ $qrisUrl }}"
                    alt="QRIS {{ $kosName }}"
                    class="w-44 h-44 rounded-xl border border-gray-200 object-contain bg-white shadow-sm"
                >
            </div>

            {{-- Instructions --}}
            <div class="flex-1 text-sm text-gray-600 dark:text-gray-300 space-y-3">
                <p class="font-semibold text-gray-800 dark:text-gray-100">
                    Cara Bayar via QRIS
                </p>
                <ol class="list-decimal list-inside space-y-1.5 text-sm">
                    <li>Buka aplikasi m-banking atau dompet digital (GoPay, OVO, Dana, ShopeePay, dll.)</li>
                    <li>Pilih menu <strong>Scan QR</strong> atau <strong>QRIS</strong></li>
                    <li>Arahkan kamera ke kode QR di samping</li>
                    <li>Masukkan nominal sesuai tagihan, lalu konfirmasi</li>
                    <li>Simpan bukti transfer dan kirim ke pengelola kos</li>
                </ol>

                @if ($phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $phone) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 mt-2 text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400">
                        <x-filament::icon icon="heroicon-o-chat-bubble-left-ellipsis" class="h-4 w-4" />
                        Konfirmasi ke Pengelola via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
