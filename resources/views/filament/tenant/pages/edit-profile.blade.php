<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Informasi kamar (read-only) --}}
        <x-filament::section>
            <x-slot name="heading">Informasi Kamar</x-slot>

            <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Login Email</p>
                    <p class="mt-1 font-mono text-gray-800 dark:text-gray-200">{{ auth()->user()->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor Kamar</p>
                    <p class="mt-1 text-gray-800 dark:text-gray-200">{{ auth()->user()->room_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Kos</p>
                    <p class="mt-1 text-gray-800 dark:text-gray-200">{{ auth()->user()->juragan?->kos_name ?? '—' }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Profile form --}}
        <form wire:submit="saveProfile">
            {{ $this->profileForm }}

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Simpan Profil
                </x-filament::button>
            </div>
        </form>

        {{-- Password form --}}
        <form wire:submit="savePassword">
            {{ $this->passwordForm }}

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit" color="warning">
                    Ubah Password
                </x-filament::button>
            </div>
        </form>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
