<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($tenants->count() > 0)
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600">
                        <x-heroicon-o-bell-alert class="w-4 h-4"/>
                    </span>
                @else
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">
                        <x-heroicon-o-check-circle class="w-4 h-4"/>
                    </span>
                @endif
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        Auto-Tagihan Hari Ini
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $today->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>

            @if($tenants->count() > 0)
                <button
                    wire:click="createAllBills"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-not-allowed"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                >
                    <x-heroicon-o-bolt class="w-4 h-4"/>
                    <span wire:loading.remove>Buat Semua ({{ $tenants->count() }})</span>
                    <span wire:loading class="hidden">Memproses…</span>
                </button>
            @endif
        </div>

        {{-- Tenants due today --}}
        @if($tenants->count() > 0)
            <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama / Kamar</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tgl. Masuk</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nominal</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($tenants as $tenant)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Kamar {{ $tenant->room_number ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ $tenant->move_in_date->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white tabular-nums">
                                Rp {{ number_format($tenant->monthly_rate, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button
                                    wire:click="createBillForTenant({{ $tenant->id }})"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1 rounded-md border border-primary-300 bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-100 focus:outline-none transition-colors dark:border-primary-700 dark:bg-primary-900/30 dark:text-primary-400"
                                >
                                    <x-heroicon-o-plus class="w-3.5 h-3.5"/>
                                    Buat Tagihan
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-4 rounded-lg border border-green-100 bg-green-50 px-4 py-3 dark:border-green-900/30 dark:bg-green-900/10">
                <p class="text-sm text-green-700 dark:text-green-400">
                    Tidak ada tagihan yang perlu dibuat hari ini. Semua anak kos sudah terbill!
                </p>
            </div>
        @endif

        {{-- Upcoming (next 3 days) --}}
        @if($upcoming->count() > 0)
            <div class="mt-4">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                    Akan jatuh tagihan dalam 3 hari ke depan
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($upcoming as $tenant)
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <x-heroicon-o-clock class="w-3 h-3 text-gray-400"/>
                        <span class="font-medium">Kamar {{ $tenant->room_number ?? '?' }}</span>
                        — {{ $tenant->move_in_date->translatedFormat('d F') }}
                        <span class="text-gray-400">(Rp {{ number_format($tenant->monthly_rate, 0, ',', '.') }})</span>
                    </span>
                    @endforeach
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
