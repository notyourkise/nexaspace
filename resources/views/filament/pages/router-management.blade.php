<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Developer: pilih juragan --}}
        @if ($isDeveloper)
            <x-filament::section>
                <x-slot name="heading">Pilih Router</x-slot>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Juragan / Kos
                        </label>
                        <select
                            wire:model.live="selectedJuraganId"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100"
                        >
                            <option value="">— Pilih juragan —</option>
                            @foreach ($juraganOptions as $opt)
                                <option value="{{ $opt->id }}">
                                    {{ $opt->kos_name }}
                                    @if ($opt->mikrotik_host)
                                        ({{ $opt->mikrotik_host }})
                                    @else
                                        (router global)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Koneksi status --}}
        @if ($juragan)
            <x-filament::section>
                <x-slot name="heading">Status Koneksi — {{ $juragan->kos_name }}</x-slot>

                @php
                    $info = \App\Services\MikroTikService::forJuragan($juragan)->connectionInfo();
                @endphp

                <div class="flex flex-wrap gap-6 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Host:</span>
                        <span class="ml-1 font-mono font-medium">{{ $info['host'] ?: '(global .env)' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Port:</span>
                        <span class="ml-1 font-mono font-medium">{{ $info['port'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">User:</span>
                        <span class="ml-1 font-mono font-medium">{{ $info['user'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                        @if (! $configured)
                            <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                Tidak dikonfigurasi
                            </span>
                        @elseif ($connected)
                            <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900 dark:text-green-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                Terhubung
                            </span>
                        @else
                            <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900 dark:text-red-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Tidak terhubung
                            </span>
                        @endif
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- DHCP Lease Table --}}
        @if ($juragan && $configured)
            <x-filament::section>
                <x-slot name="heading">
                    DHCP Leases
                    @if (count($leases) > 0)
                        <span class="ml-2 text-xs font-normal text-gray-500">({{ count($leases) }} entri)</span>
                    @endif
                </x-slot>

                @if (! $connected)
                    <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300">
                        Router tidak dapat dijangkau. Periksa koneksi jaringan atau konfigurasi MikroTik.
                    </div>
                @elseif (count($leases) === 0)
                    <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 text-sm text-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                        Tidak ada DHCP lease aktif ditemukan.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">MAC Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hostname</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rate Limit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expires</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($leases as $lease)
                                    @php
                                        $status    = $lease['status'] ?? $lease['disabled'] ?? '';
                                        $rateLimit = $lease['rate-limit'] ?? '';
                                        $isThrottled = $rateLimit !== '';
                                        $mac       = $lease['mac-address'] ?? '';
                                        $ip        = $lease['address'] ?? '';
                                        $hostname  = $lease['host-name'] ?? $lease['comment'] ?? '—';
                                        $expires   = $lease['expires-after'] ?? $lease['lease-time'] ?? '—';
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-2.5 font-mono text-gray-900 dark:text-gray-100">{{ $mac }}</td>
                                        <td class="px-4 py-2.5 font-mono text-gray-700 dark:text-gray-300">{{ $ip }}</td>
                                        <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400">{{ $hostname }}</td>
                                        <td class="px-4 py-2.5">
                                            @if ($status === 'bound' || $status === 'false')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900 dark:text-green-300">Aktif</span>
                                            @elseif ($status === 'waiting')
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">Menunggu</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $status ?: '—' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5">
                                            @if ($isThrottled)
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900 dark:text-red-300 font-mono">{{ $rateLimit }}</span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600 text-xs">Tidak dibatasi</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 text-xs font-mono">{{ $expires }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-filament::section>
        @elseif (! $juragan)
            @if ($isDeveloper)
                <x-filament::section>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Pilih juragan di atas untuk melihat data router dan DHCP leases.
                    </div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4 text-sm text-yellow-700 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-300">
                        Router MikroTik belum dikonfigurasi. Hubungi developer NexaSpace untuk mengatur koneksi router.
                    </div>
                </x-filament::section>
            @endif
        @endif

    </div>
</x-filament-panels::page>
