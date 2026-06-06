<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Info platform (read-only) --}}
        <x-filament::section>
            <x-slot name="heading">Informasi Platform</x-slot>

            <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Email Login</p>
                    <p class="mt-1 font-mono text-gray-800 dark:text-gray-200">{{ auth()->user()->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Role</p>
                    <p class="mt-1 font-medium text-gray-800 dark:text-gray-200">Developer / Super Admin</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Platform</p>
                    <p class="mt-1 font-medium text-gray-800 dark:text-gray-200">NexaSpace</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Profil + Rekening + QRIS form --}}
        <form wire:submit="saveInfo">
            {{ $this->infoForm }}

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Simpan Profil & Rekening
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
