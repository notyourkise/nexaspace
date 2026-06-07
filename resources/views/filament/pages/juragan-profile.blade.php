<x-filament-panels::page>
    <div class="space-y-6 nxt-juraganprofile">

        {{-- Info kos (read-only) --}}
        @php($profileUser = auth()->user())

        <style>
            /* ── Jarak proporsional antar card / form ── */
            .nxt-juraganprofile { display: flex; flex-direction: column; gap: 2rem; }
            .nxt-juraganprofile > * + * { margin-top: 0 !important; }
            .nxt-juraganprofile .fi-section + .fi-section { margin-top: 1.75rem !important; }

            .nxp-info-card {
                overflow: hidden;
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 0.85rem;
                background: #05070b;
                box-shadow: 0 20px 50px rgba(0,0,0,0.22);
            }

            .nxp-info-header {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.8rem 1rem;
                border-bottom: 1px solid rgba(255,255,255,0.10);
            }

            .nxp-info-header h2 {
                margin: 0;
                color: #ffffff;
                font-size: 1rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .nxp-info-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .nxp-info-item {
                display: flex;
                align-items: center;
                min-width: 0;
                gap: 0.8rem;
                padding: 1rem 1.1rem;
            }

            .nxp-info-item + .nxp-info-item {
                border-left: 1px solid rgba(255,255,255,0.10);
            }

            .nxp-info-icon,
            .nxp-info-main-icon {
                display: grid;
                place-items: center;
                flex: 0 0 auto;
                color: #7cff47;
                background: rgba(34,197,94,0.10);
                box-shadow: inset 0 0 0 1px rgba(124,255,71,0.22);
            }

            .nxp-info-main-icon {
                width: 2rem;
                height: 2rem;
                border-radius: 0.55rem;
            }

            .nxp-info-icon {
                width: 2.55rem;
                height: 2.55rem;
                border-radius: 999px;
            }

            .nxp-info-label {
                margin: 0;
                color: rgba(255,255,255,0.70);
                font-size: 0.76rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .nxp-info-value {
                margin: 0.28rem 0 0;
                color: #ffffff;
                font-size: 0.9rem;
                font-weight: 900;
                line-height: 1.25;
            }

            .nxp-info-value.mono {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            }

            @media (max-width: 1180px) {
                .nxp-info-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .nxp-info-item:nth-child(3) {
                    border-left: 0;
                }

                .nxp-info-item:nth-child(n + 3) {
                    border-top: 1px solid rgba(255,255,255,0.10);
                }
            }

            @media (max-width: 680px) {
                .nxp-info-grid {
                    grid-template-columns: 1fr;
                }

                .nxp-info-item + .nxp-info-item {
                    border-left: 0;
                    border-top: 1px solid rgba(255,255,255,0.10);
                }
            }
        </style>

        <section class="nxp-info-card">
            <div class="nxp-info-header">
                <div class="nxp-info-main-icon">
                    <svg style="width:1.1rem;height:1.1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M6 21V3m12 18V3M9 7.5h1.5M9 11.25h1.5M13.5 7.5H15m-1.5 3.75H15M9 21v-4.5h6V21" />
                    </svg>
                </div>
                <h2>Informasi Kos</h2>
            </div>

            <div class="nxp-info-grid">
                <div class="nxp-info-item">
                    <div class="nxp-info-icon">
                        <svg style="width:1.3rem;height:1.3rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5v9a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25h-15A2.25 2.25 0 0 0 2.25 7.5m19.5 0-8.57 5.28a2.25 2.25 0 0 1-2.36 0L2.25 7.5" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-info-label">Email Login</p>
                        <p class="nxp-info-value mono">{{ $profileUser->email }}</p>
                    </div>
                </div>

                <div class="nxp-info-item">
                    <div class="nxp-info-icon">
                        <svg style="width:1.3rem;height:1.3rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M6 21V3m12 18V3M9 8.25h1.5M9 12h1.5M13.5 8.25H15M13.5 12H15M9 21v-4.5h6V21" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-info-label">Nama Kos</p>
                        <p class="nxp-info-value">{{ $profileUser->kos_name ?? '-' }}</p>
                    </div>
                </div>

                <div class="nxp-info-item">
                    <div class="nxp-info-icon">
                        <svg style="width:1.3rem;height:1.3rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.169.659 1.591l8.182 8.182a2.25 2.25 0 0 0 3.182 0l4.318-4.318a2.25 2.25 0 0 0 0-3.182L11.16 3.659A2.25 2.25 0 0 0 9.568 3Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.008v.008H6.75V6.75Z" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-info-label">Paket</p>
                        <p class="nxp-info-value">{{ strtoupper($profileUser->plan ?? '-') }}</p>
                    </div>
                </div>

                <div class="nxp-info-item">
                    <div class="nxp-info-icon">
                        <svg style="width:1.3rem;height:1.3rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125V21M3 21h18M4.5 21V8.25L12 3l7.5 5.25V21" />
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p class="nxp-info-label">Kuota Kamar</p>
                        <p class="nxp-info-value">{{ $profileUser->room_quota ?? 0 }} kamar</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Profil + Rekening + QRIS form --}}
        <form wire:submit="saveInfo">
            {{ $this->infoForm }}

            <div class="mt-4 flex flex-wrap justify-end gap-3">
                @if ($profileUser->qris_image)
                    <x-filament::button
                        type="button"
                        color="danger"
                        icon="heroicon-o-trash"
                        wire:click="deleteQris"
                        wire:confirm="Hapus gambar QRIS saat ini? QRIS akan langsung dihapus dari portal anak kos dan invoice."
                    >
                        Hapus QRIS
                    </x-filament::button>
                @endif

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
