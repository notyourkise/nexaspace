<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-wifi" class="h-5 w-5 text-primary-500" />
                <span>Status Router MikroTik</span>
            </div>
        </x-slot>

        <div class="flex items-start justify-between gap-4">
            {{-- Status badge --}}
            <div>
                @if (! $configured)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                        Belum Dikonfigurasi
                    </span>
                    <p class="mt-2 text-xs text-gray-500">
                        Isi <code class="rounded bg-gray-100 px-1 py-0.5 text-xs dark:bg-gray-800">MIKROTIK_HOST</code> di file <code class="rounded bg-gray-100 px-1 py-0.5 text-xs dark:bg-gray-800">.env</code>.
                    </p>
                @elseif ($connected)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-success-100 px-3 py-1 text-sm font-medium text-success-700 dark:bg-success-900 dark:text-success-300">
                        <span class="h-2 w-2 rounded-full bg-success-500 animate-pulse"></span>
                        Terhubung
                    </span>
                    <p class="mt-2 text-xs text-gray-500">Router merespons dengan normal.</p>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-danger-100 px-3 py-1 text-sm font-medium text-danger-700 dark:bg-danger-900 dark:text-danger-300">
                        <span class="h-2 w-2 rounded-full bg-danger-500"></span>
                        Tidak Terhubung
                    </span>
                    <p class="mt-2 text-xs text-danger-600 dark:text-danger-400">
                        Router tidak dapat dihubungi. Periksa host, port, dan kredensial.
                        Throttle otomatis mungkin tidak berfungsi.
                    </p>
                @endif
            </div>

            {{-- Connection details --}}
            @if ($configured)
                <div class="shrink-0 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-xs dark:border-gray-700 dark:bg-gray-900">
                    <table class="space-y-1">
                        <tr>
                            <td class="pr-3 text-gray-500">Host</td>
                            <td class="font-mono font-medium text-gray-800 dark:text-gray-200">{{ $host }}</td>
                        </tr>
                        <tr>
                            <td class="pr-3 text-gray-500">Port</td>
                            <td class="font-mono font-medium text-gray-800 dark:text-gray-200">{{ $port }}</td>
                        </tr>
                        <tr>
                            <td class="pr-3 text-gray-500">User</td>
                            <td class="font-mono font-medium text-gray-800 dark:text-gray-200">{{ $user }}</td>
                        </tr>
                    </table>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
