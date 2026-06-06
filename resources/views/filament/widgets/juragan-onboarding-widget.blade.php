<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-rocket-launch" class="h-5 w-5 text-primary-500" />
                <span>Setup Kos Anda — {{ $completedCount }}/{{ count($steps) }} Selesai</span>
            </div>
        </x-slot>

        <div class="space-y-3">
            @foreach ($steps as $step)
                <div class="flex items-start gap-3 rounded-lg border p-3
                    {{ $step['done']
                        ? 'border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-950'
                        : 'border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-950' }}">

                    <div class="mt-0.5 shrink-0">
                        @if ($step['done'])
                            <x-filament::icon
                                icon="heroicon-o-check-circle"
                                class="h-5 w-5 text-success-600 dark:text-success-400"
                            />
                        @else
                            <x-filament::icon
                                icon="heroicon-o-exclamation-circle"
                                class="h-5 w-5 text-warning-600 dark:text-warning-400"
                            />
                        @endif
                    </div>

                    <div>
                        <p class="text-sm font-medium
                            {{ $step['done'] ? 'text-success-800 dark:text-success-200' : 'text-warning-800 dark:text-warning-200' }}">
                            {{ $step['label'] }}
                        </p>
                        <p class="mt-0.5 text-xs
                            {{ $step['done'] ? 'text-success-600 dark:text-success-400' : 'text-warning-600 dark:text-warning-400' }}">
                            {{ $step['note'] }}
                        </p>
                    </div>
                </div>
            @endforeach

            @if ($noRateRooms->isNotEmpty())
                <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Kamar yang perlu diisi tarif:
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($noRateRooms as $room)
                            <span class="inline-flex items-center rounded-full bg-warning-100 px-2.5 py-0.5 text-xs font-medium text-warning-800 dark:bg-warning-900 dark:text-warning-200">
                                Kamar {{ $room->room_number ?? '?' }} — {{ $room->name }}
                            </span>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Buka menu <strong>Anak Kos</strong> → edit anak kos → isi kolom <strong>Monthly Rate</strong>.
                    </p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
