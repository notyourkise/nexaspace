<x-filament-widgets::widget>
    <style>
        .nxj-setup {
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 0.9rem;
            background:
                radial-gradient(circle at 18% 0%, rgba(124,255,71,0.10), transparent 20rem),
                #05070b;
            box-shadow: 0 20px 50px rgba(0,0,0,0.22);
        }

        .nxj-setup-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.15rem;
            border-bottom: 1px solid rgba(255,255,255,0.10);
        }

        .nxj-setup-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .nxj-setup-title-icon,
        .nxj-step-icon {
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            color: #7cff47;
            background: rgba(34,197,94,0.10);
            box-shadow: inset 0 0 0 1px rgba(124,255,71,0.22);
        }

        .nxj-setup-title-icon {
            width: 2.15rem;
            height: 2.15rem;
            border-radius: 0.55rem;
        }

        .nxj-setup-title h3 {
            margin: 0;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .nxj-setup-title p {
            margin: 0.25rem 0 0;
            color: rgba(255,255,255,0.62);
            font-size: 0.78rem;
            font-weight: 700;
        }

        .nxj-setup-pill {
            flex: 0 0 auto;
            border-radius: 999px;
            padding: 0.45rem 0.75rem;
            color: #7cff47;
            background: rgba(34,197,94,0.10);
            border: 1px solid rgba(124,255,71,0.24);
            font-size: 0.78rem;
            font-weight: 900;
        }

        .nxj-setup-body {
            padding: 1rem 1.15rem 1.15rem;
        }

        .nxj-progress {
            height: 0.45rem;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
        }

        .nxj-progress-bar {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #22c55e, #7cff47);
            box-shadow: 0 0 20px rgba(34,197,94,0.45);
            transition: width 0.25s ease;
        }

        .nxj-step-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(12rem, 1fr));
            gap: 0.8rem;
            margin-top: 1rem;
        }

        .nxj-step {
            display: flex;
            gap: 0.75rem;
            min-width: 0;
            padding: 0.9rem;
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 0.75rem;
            background: rgba(255,255,255,0.035);
        }

        .nxj-step.pending {
            border-color: rgba(245,158,11,0.28);
            background: rgba(245,158,11,0.07);
        }

        .nxj-step-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
        }

        .nxj-step.pending .nxj-step-icon {
            color: #fbbf24;
            background: rgba(245,158,11,0.12);
            box-shadow: inset 0 0 0 1px rgba(245,158,11,0.28);
        }

        .nxj-step-label {
            margin: 0;
            color: #ffffff;
            font-size: 0.86rem;
            font-weight: 900;
            line-height: 1.25;
        }

        .nxj-step-note {
            margin: 0.28rem 0 0;
            color: rgba(255,255,255,0.62);
            font-size: 0.76rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .nxj-rate-panel {
            margin-top: 1rem;
            border: 1px solid rgba(245,158,11,0.24);
            border-radius: 0.8rem;
            background: rgba(245,158,11,0.06);
            overflow: hidden;
        }

        .nxj-rate-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 0.95rem;
            border-bottom: 1px solid rgba(245,158,11,0.18);
        }

        .nxj-rate-head p {
            margin: 0;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 900;
        }

        .nxj-rate-link {
            flex: 0 0 auto;
            border-radius: 0.55rem;
            padding: 0.45rem 0.7rem;
            color: #7cff47;
            border: 1px solid rgba(124,255,71,0.30);
            background: rgba(34,197,94,0.09);
            font-size: 0.76rem;
            font-weight: 900;
            text-decoration: none;
        }

        .nxj-room-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(10.5rem, 1fr));
            gap: 0.55rem;
            max-height: 12rem;
            overflow: auto;
            padding: 0.9rem;
        }

        .nxj-room-chip {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            min-width: 0;
            border-radius: 0.65rem;
            padding: 0.55rem 0.65rem;
            color: #ffffff;
            background: rgba(5,7,11,0.62);
            border: 1px solid rgba(255,255,255,0.10);
        }

        .nxj-room-chip strong {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.78rem;
            font-weight: 900;
        }

        .nxj-room-chip span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: rgba(255,255,255,0.58);
            font-size: 0.72rem;
            font-weight: 700;
        }

        .nxj-rate-foot {
            padding: 0 0.95rem 0.9rem;
            color: rgba(255,255,255,0.62);
            font-size: 0.76rem;
            font-weight: 700;
        }

        .nxj-bank-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1rem;
            padding: 0.95rem 1rem;
            border: 1px solid rgba(245,158,11,0.24);
            border-radius: 0.8rem;
            background: rgba(245,158,11,0.06);
        }

        .nxj-bank-panel p {
            margin: 0;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 900;
        }

        .nxj-bank-panel span {
            display: block;
            margin-top: 0.25rem;
            color: rgba(255,255,255,0.62);
            font-size: 0.76rem;
            font-weight: 700;
        }

        @media (max-width: 920px) {
            .nxj-step-grid {
                grid-template-columns: 1fr;
            }

            .nxj-setup-head,
            .nxj-rate-head,
            .nxj-bank-panel {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    @php($progressPct = count($steps) > 0 ? round(($completedCount / count($steps)) * 100) : 0)

    <section class="nxj-setup">
        <div class="nxj-setup-head">
            <div class="nxj-setup-title">
                <div class="nxj-setup-title-icon">
                    <svg style="width:1.15rem;height:1.15rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.63 8.41m5.96 5.96a14.93 14.93 0 0 1-5.96 5.96M9.63 8.41a6 6 0 0 0-7.38 5.84h4.8" />
                    </svg>
                </div>
                <div>
                    <h3>Setup Kos Anda</h3>
                    <p>Lengkapi konfigurasi dasar agar billing dan operasional berjalan rapi.</p>
                </div>
            </div>
            <div class="nxj-setup-pill">{{ $completedCount }}/{{ count($steps) }} Selesai</div>
        </div>

        <div class="nxj-setup-body">
            <div class="nxj-progress" aria-label="Progress setup kos">
                <div class="nxj-progress-bar" style="width: {{ $progressPct }}%;"></div>
            </div>

            <div class="nxj-step-grid">
                @foreach ($steps as $step)
                    <div class="nxj-step {{ $step['done'] ? '' : 'pending' }}">
                        <div class="nxj-step-icon">
                            @if ($step['done'])
                                <svg style="width:1.1rem;height:1.1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            @else
                                <svg style="width:1.1rem;height:1.1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                                </svg>
                            @endif
                        </div>
                        <div style="min-width:0;">
                            <p class="nxj-step-label">{{ $step['label'] }}</p>
                            <p class="nxj-step-note">{{ $step['note'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($noRateRooms->isNotEmpty())
                <div class="nxj-rate-panel">
                    <div class="nxj-rate-head">
                        <p>Kamar yang perlu diisi tarif ({{ $noRateRooms->count() }})</p>
                        <a class="nxj-rate-link" href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}">
                            Buka Anak Kos
                        </a>
                    </div>

                    <div class="nxj-room-list">
                        @foreach ($noRateRooms as $room)
                            <div class="nxj-room-chip">
                                <strong>Kamar {{ $room->room_number ?? '?' }}</strong>
                                <span>{{ $room->name }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="nxj-rate-foot">
                        Buka menu Anak Kos, edit anak kos, lalu isi kolom Monthly Rate.
                    </div>
                </div>
            @endif

            @unless ($hasBank)
                <div class="nxj-bank-panel">
                    <div style="min-width:0;">
                        <p>Rekening bank belum diisi</p>
                        <span>Isi nomor rekening di menu Profil &amp; Rekening agar anak kos bisa memilihnya saat membayar tagihan.</span>
                    </div>
                    <a class="nxj-rate-link" href="{{ $profileUrl }}">
                        Buka Profil &amp; Rekening
                    </a>
                </div>
            @endunless
        </div>
    </section>
</x-filament-widgets::widget>
