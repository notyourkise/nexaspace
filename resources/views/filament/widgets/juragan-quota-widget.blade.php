<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Manajemen Kuota Kamar
        </x-slot>
        <x-slot name="description">
            {{ $juragan->kos_name }} &mdash; Paket {{ strtoupper($juragan->plan ?? '—') }}
        </x-slot>

        <div class="space-y-4">

            {{-- Progress bar kuota --}}
            <div>
                <div class="flex items-center justify-between mb-1 text-sm font-medium">
                    <span class="text-gray-700 dark:text-gray-200">
                        {{ $used }} / {{ $quota > 0 ? $quota : '∞' }} kamar terisi
                    </span>
                    <span @class([
                        'font-semibold',
                        'text-green-600'  => $pct < 80,
                        'text-yellow-600' => $pct >= 80 && $pct < 100,
                        'text-red-600'    => $pct >= 100,
                    ])>
                        {{ $pct }}%
                    </span>
                </div>

                @if ($quota > 0)
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                        <div
                            class="h-3 rounded-full transition-all duration-500"
                            style="width: {{ min($pct, 100) }}%; background-color: {{ $pct >= 100 ? '#dc2626' : ($pct >= 80 ? '#d97706' : '#306D29') }};"
                        ></div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        @if ($sisa > 0)
                            {{ $sisa }} slot kamar masih tersedia
                        @else
                            Kuota penuh &mdash; tidak bisa menambah akun anak kos
                        @endif
                    </p>
                @else
                    <p class="text-xs text-gray-400">Kuota tidak terdefinisi di akun ini.</p>
                @endif
            </div>

            {{-- Warning: kamar tanpa rate bulanan --}}
            @if ($noRate->isNotEmpty())
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 dark:border-yellow-700 dark:bg-yellow-900/20">
                    <div class="flex items-start gap-2">
                        <x-filament::icon
                            icon="heroicon-o-exclamation-triangle"
                            class="mt-0.5 h-4 w-4 shrink-0 text-yellow-600"
                        />
                        <div>
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                {{ $noRate->count() }} kamar belum punya rate bulanan
                            </p>
                            <p class="mt-0.5 text-xs text-yellow-700 dark:text-yellow-400">
                                Kamar tanpa rate tidak akan digenerate tagihan otomatis setiap bulannya.
                            </p>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach ($noRate as $t)
                                    <span class="inline-flex items-center rounded-md bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                        {{ $t->room_number ? "Kamar {$t->room_number}" : $t->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-700 dark:bg-green-900/20">
                    <div class="flex items-center gap-2">
                        <x-filament::icon
                            icon="heroicon-o-check-circle"
                            class="h-4 w-4 text-green-600"
                        />
                        <p class="text-sm font-medium text-green-800 dark:text-green-300">
                            Semua kamar sudah memiliki rate bulanan
                        </p>
                    </div>
                </div>
            @endif

            {{-- Daftar kamar ringkas --}}
            @if ($tenants->isNotEmpty())
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Daftar Kamar ({{ $used }})
                    </p>
                    <div class="grid grid-cols-2 gap-1 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($tenants as $tenant)
                            <div @class([
                                'flex items-center justify-between rounded-md border px-2 py-1 text-xs',
                                'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' => (int)$tenant->monthly_rate > 0,
                                'border-yellow-200 bg-yellow-50 dark:border-yellow-700 dark:bg-yellow-900/20' => (int)$tenant->monthly_rate === 0,
                            ])>
                                <span class="font-medium text-gray-700 dark:text-gray-200">
                                    {{ $tenant->room_number ? "Kamar {$tenant->room_number}" : $tenant->name }}
                                </span>
                                <span @class([
                                    'text-gray-500' => (int)$tenant->monthly_rate > 0,
                                    'text-yellow-600' => (int)$tenant->monthly_rate === 0,
                                ])>
                                    {{ (int)$tenant->monthly_rate > 0 ? 'Rp ' . number_format($tenant->monthly_rate, 0, ',', '.') : '—' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
