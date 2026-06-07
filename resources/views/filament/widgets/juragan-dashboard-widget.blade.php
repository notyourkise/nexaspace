<x-filament-widgets::widget class="nxd-widget">
<div class="nxd-root">
    <div class="nxd-hero-row">
        <div class="nxd-card nxd-welcome">
            <div class="nxd-glow"></div>
            <svg class="nxd-art" viewBox="0 0 800 280" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M800 45 C680 45 580 125 440 96 C300 67 180 150 0 118" stroke="#22c55e" stroke-width="1.5" fill="none" opacity="0.42"/>
                <path d="M800 105 C680 105 580 185 440 156 C300 127 180 210 0 178" stroke="#22c55e" stroke-width="1.2" fill="none" opacity="0.32"/>
                <path d="M800 165 C680 165 580 245 440 216 C300 187 180 268 0 238" stroke="#22c55e" stroke-width="1" fill="none" opacity="0.2"/>
                <circle cx="660" cy="140" r="66" stroke="#22c55e" stroke-width="1" fill="none" opacity="0.16"/>
                <circle cx="660" cy="140" r="41" stroke="#22c55e" stroke-width="0.8" fill="rgba(34,197,94,0.04)" opacity="0.26"/>
                <circle cx="660" cy="140" r="11" fill="rgba(34,197,94,0.18)" opacity="0.55"/>
            </svg>

            <div class="nxd-welcome-inner">
                <div>
                    <p class="nxd-welcome-tag">{{ $kosName }} - Paket {{ $plan }}</p>
                    <h1 class="nxd-welcome-name">{{ $userName }}</h1>
                    <p class="nxd-welcome-sub">Ringkasan kos, tagihan, perangkat, dan laporan anak kos.</p>
                </div>
                <div class="nxd-welcome-foot">
                    <div class="nxd-meta">
                        <div class="nxd-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="nxd-meta-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25" />
                            </svg>
                            <span>{{ $dateStr }}</span>
                        </div>
                        <div class="nxd-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="nxd-meta-ico" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-data="{t:'',init(){const f={hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:false,timeZone:'Asia/Makassar'};const tick=()=>{this.t=new Intl.DateTimeFormat('id-ID',f).format(new Date())};tick();setInterval(tick,1000)}}" x-text="t+' WITA'">{{ \Carbon\Carbon::now('Asia/Makassar')->format('H:i:s') }} WITA</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                        @csrf
                        <button type="submit" class="nxd-signout">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="nxd-card nxd-overview">
            <div class="nxd-ov-head">
                <div class="nxd-ov-dot"></div>
                <div>
                    <p class="nxd-ov-title">Kos Overview</p>
                    <p class="nxd-ov-sub">Status operasional kos dan langganan</p>
                </div>
            </div>
            <div class="nxd-ov-list">
                <div class="nxd-ov-item">
                    <div class="nxd-ov-label"><span>Kuota Kamar</span></div>
                    <span class="nxd-green nxd-bold">{{ $tenantCount }} / {{ $quota ?: '-' }}</span>
                </div>
                <div class="nxd-ov-item">
                    <div class="nxd-ov-label"><span>Langganan</span></div>
                    <span class="{{ $subscriptionTone === 'green' ? 'nxd-green' : ($subscriptionTone === 'red' ? 'nxd-red' : 'nxd-amber') }} nxd-bold">{{ $subscriptionLabel }}</span>
                </div>
                <div class="nxd-ov-item">
                    <div class="nxd-ov-label"><span>Jatuh Tempo</span></div>
                    <span>{{ $subscriptionDue }}</span>
                </div>
                <div class="nxd-ov-item">
                    <div class="nxd-ov-label"><span>Tagihan Hari Ini</span></div>
                    <span class="nxd-green nxd-bold">{{ $billingDueToday }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="nxd-stats">
        @php
            $stats = [
                ['Kamar Terisi', $quota ? "{$tenantCount}/{$quota}" : $tenantCount, $quotaSisa > 0 ? "{$quotaSisa} slot tersisa" : 'Kuota penuh', 'M3 21h18M4.5 3h15M6 21V3m12 18V3M9 7h1.5M9 11h1.5M13.5 7H15M13.5 11H15M9 21v-4.5h6V21'],
                ['Perangkat Aktif', $activeDevices, $throttledDevices > 0 ? "{$throttledDevices} di-throttle" : 'Semua normal', 'M10.5 1.5h3M12 18h.01M7.5 3.75v16.5h9V3.75h-9Z'],
                ['Tagihan Belum Lunas', $unpaidBills, $throttledBills > 0 ? "{$throttledBills} dibatasi" : 'Belum di-throttle', 'M12 9v3.75M12 15.75h.007M3 19.5h18L12 3 3 19.5Z'],
                ['Pendapatan Bulan Ini', 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'Tagihan lunas bulan ini', 'M2.25 8.25h19.5M4.5 19.5h15A2.25 2.25 0 0021.75 17.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5Z'],
                ['Laporan Aktif', $openReports, 'Butuh follow up', 'M9 12h6M9 15h6M9 18h3M5 4.5h14v15H5v-15Z'],
                ['Laporan Selesai', $resolvedReports, 'Selesai bulan ini', 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z'],
                ['Sisa Kuota', $quota ? $quotaSisa : '-', $quotaPct . '% terpakai', 'M15 19.128A9.38 9.38 0 0112 19.5c-2.331 0-4.512-.645-6.374-1.766A6.375 6.375 0 0117.59 15.05'],
                ['Paket Kos', $plan, $kosName, 'M2.25 8.25h19.5M6.75 15h10.5M6.75 18h7.5M4.5 4.5h15v15h-15v-15Z'],
            ];
        @endphp

        @foreach ($stats as [$label, $value, $desc, $path])
            <div class="nxd-stat">
                <div class="nxd-stat-top">
                    <div class="nxd-stat-ico">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1.15rem;height:1.15rem;" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                        </svg>
                    </div>
                    <span class="nxd-stat-label">{{ $label }}</span>
                </div>
                <p class="nxd-stat-val {{ is_string($value) && strlen($value) > 12 ? 'nxd-stat-val-sm' : '' }}">{{ $value }}</p>
                <p class="nxd-stat-desc">{{ $desc }}</p>
            </div>
        @endforeach
    </div>

    <div class="nxd-bottom">
        <div class="nxd-card nxd-mk">
            <div class="nxd-mk-head">
                <svg xmlns="http://www.w3.org/2000/svg" class="nxd-mk-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/>
                </svg>
                <p class="nxd-mk-title">Status Router MikroTik</p>
            </div>
            <div class="nxd-mk-body">
                @if (! $mkConfigured)
                    <div class="nxd-mk-badge nxd-mk-badge-gray"><span class="nxd-mk-dot nxd-mk-dot-gray"></span>Belum Dikonfigurasi</div>
                    <p class="nxd-mk-hint">Isi konfigurasi router di menu Router MikroTik.</p>
                @elseif ($mkConnected)
                    <div class="nxd-mk-badge nxd-mk-badge-green"><span class="nxd-mk-dot nxd-mk-dot-green"></span>Terhubung</div>
                    <p class="nxd-mk-hint">Router merespons dengan normal.</p>
                    <div class="nxd-mk-table">
                        <div class="nxd-mk-row"><span class="nxd-mk-key">Host</span><span class="nxd-mk-val">{{ $mkHost }}</span></div>
                        <div class="nxd-mk-row"><span class="nxd-mk-key">Port</span><span class="nxd-mk-val">{{ $mkPort }}</span></div>
                        <div class="nxd-mk-row"><span class="nxd-mk-key">User</span><span class="nxd-mk-val">{{ $mkUser }}</span></div>
                    </div>
                @else
                    <div class="nxd-mk-badge nxd-mk-badge-red"><span class="nxd-mk-dot nxd-mk-dot-red"></span>Tidak Terhubung</div>
                    <p class="nxd-mk-hint nxd-mk-hint-red">Router tidak dapat dihubungi.</p>
                @endif
            </div>
        </div>

        <div class="nxd-card nxd-chart">
            <div class="nxd-chart-head">
                <div>
                    <p class="nxd-chart-title">Pendapatan 6 Bulan Terakhir</p>
                    <p class="nxd-chart-sub">Revenue dari tagihan anak kos {{ $kosName }}</p>
                </div>
            </div>
            <div class="nxd-chart-body">
                <svg viewBox="0 0 580 200" xmlns="http://www.w3.org/2000/svg" aria-label="Revenue chart">
                    <defs><linearGradient id="nxJuraganAreaGrad" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#22c55e" stop-opacity="0.25"/><stop offset="100%" stop-color="#22c55e" stop-opacity="0"/></linearGradient></defs>
                    @foreach ($svgYLabels as $yl)
                        <line x1="{{ $svgCLeft }}" y1="{{ $yl['y'] }}" x2="{{ $svgCRight }}" y2="{{ $yl['y'] }}" stroke="rgba(255,255,255,0.05)" stroke-width="1" stroke-dasharray="4,4"/>
                        <text x="{{ $svgCLeft - 4 }}" y="{{ $yl['y'] + 4 }}" text-anchor="end" font-size="9" fill="rgba(148,163,184,0.6)">{{ $yl['text'] }}</text>
                    @endforeach
                    <path d="{{ $svgArea }}" fill="url(#nxJuraganAreaGrad)"/>
                    <path d="{{ $svgLine }}" stroke="#22c55e" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    @foreach ($svgPts as $pt)
                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3.5" fill="#22c55e" stroke="#07090D" stroke-width="1.5"/>
                    @endforeach
                    @foreach ($chartLabels as $i => $label)
                        @php($xPos = count($chartLabels) > 1 ? $svgCLeft + ($i / (count($chartLabels) - 1)) * ($svgCRight - $svgCLeft) : $svgCLeft)
                        <text x="{{ round($xPos, 1) }}" y="192" text-anchor="middle" font-size="9.5" fill="rgba(148,163,184,0.7)">{{ $label }}</text>
                    @endforeach
                </svg>
            </div>
        </div>
    </div>
</div>
</x-filament-widgets::widget>
