<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Info platform (read-only) --}}
        @php($profileUser = auth()->user())

        <style>
            .nxp-platform-card {
                overflow: hidden;
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 0.85rem;
                background: #05070b;
                box-shadow: 0 20px 50px rgba(0,0,0,0.22);
            }

            .nxp-platform-header {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.8rem 1rem;
                border-bottom: 1px solid rgba(255,255,255,0.10);
            }

            .nxp-platform-header h2 {
                margin: 0;
                color: #ffffff;
                font-size: 1rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .nxp-platform-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .nxp-platform-item {
                display: flex;
                align-items: center;
                min-width: 0;
                gap: 0.8rem;
                padding: 1rem 1.25rem;
            }

            .nxp-platform-item + .nxp-platform-item {
                border-left: 1px solid rgba(255,255,255,0.10);
            }

            .nxp-platform-icon,
            .nxp-platform-main-icon {
                display: grid;
                place-items: center;
                flex: 0 0 auto;
                color: #7cff47;
                background: rgba(34,197,94,0.10);
                box-shadow: inset 0 0 0 1px rgba(124,255,71,0.22);
            }

            .nxp-platform-main-icon {
                width: 2rem;
                height: 2rem;
                border-radius: 0.55rem;
            }

            .nxp-platform-icon {
                width: 2.65rem;
                height: 2.65rem;
                border-radius: 999px;
            }

            .nxp-platform-label {
                margin: 0;
                color: rgba(255,255,255,0.70);
                font-size: 0.78rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .nxp-platform-value {
                margin: 0.28rem 0 0;
                color: #ffffff;
                font-size: 0.92rem;
                font-weight: 900;
                line-height: 1.25;
            }

            .nxp-platform-value.mono {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            }

            @media (max-width: 920px) {
                .nxp-platform-grid {
                    grid-template-columns: 1fr;
                }

                .nxp-platform-item + .nxp-platform-item {
                    border-left: 0;
                    border-top: 1px solid rgba(255,255,255,0.10);
                }
            }
        </style>

        <section class="nxp-platform-card">
            <div class="nxp-platform-header">
                <div class="nxp-platform-main-icon">
                    <svg style="width:1.1rem;height:1.1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.75A2.25 2.25 0 0 1 8.25 4.5h7.5A2.25 2.25 0 0 1 18 6.75v.75H6v-.75ZM6 11.25h12v1.5H6v-1.5ZM6 16.5h12v.75a2.25 2.25 0 0 1-2.25 2.25h-7.5A2.25 2.25 0 0 1 6 17.25v-.75Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5h.008v.008H8.25V7.5Zm0 5.25h.008v.008H8.25v-.008Zm0 5.25h.008v.008H8.25V18Z" />
                    </svg>
                </div>
                <h2>Informasi Platform</h2>
            </div>

            <div class="nxp-platform-grid">
                <div class="nxp-platform-item">
                    <div class="nxp-platform-icon">
                        <svg style="width:1.35rem;height:1.35rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5v9a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25h-15A2.25 2.25 0 0 0 2.25 7.5m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615a2.25 2.25 0 0 1-1.07-1.916V7.5" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-platform-label">Email Login</p>
                        <p class="nxp-platform-value mono">
                            {{ $profileUser->email }}
                        </p>
                    </div>
                </div>

                <div class="nxp-platform-item">
                    <div class="nxp-platform-icon">
                        <svg style="width:1.35rem;height:1.35rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-platform-label">Role</p>
                        <p class="nxp-platform-value">
                            Developer / Super Admin
                        </p>
                    </div>
                </div>

                <div class="nxp-platform-item">
                    <div class="nxp-platform-icon">
                        <svg style="width:1.35rem;height:1.35rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-4.5-9 4.5 9 4.5 9-4.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m3 12 9 4.5 9-4.5M3 16.5l9 4.5 9-4.5" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-platform-label">Platform</p>
                        <p class="nxp-platform-value">
                            NexaSpace
                        </p>
                    </div>
                </div>
            </div>
        </section>

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
