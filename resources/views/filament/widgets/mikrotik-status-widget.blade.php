<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-wifi" class="h-5 w-5" style="color:#22c55e;" />
                <span style="color:#f1f5f9;font-weight:600;">Status Router MikroTik</span>
            </div>
        </x-slot>

        <div class="flex items-center justify-between gap-4">

            {{-- Status + details --}}
            <div class="flex-1 min-w-0">
                @if (! $configured)
                    <div class="flex items-center gap-2 mb-2">
                        <span style="display:inline-flex;align-items:center;gap:0.375rem;background:rgba(71,85,105,0.2);border:1px solid rgba(71,85,105,0.35);color:#94a3b8;font-size:0.8125rem;font-weight:500;padding:0.25rem 0.75rem;border-radius:9999px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#64748b;flex-shrink:0;display:inline-block;"></span>
                            Belum Dikonfigurasi
                        </span>
                    </div>
                    <p style="font-size:0.8125rem;color:#64748b;margin:0;">
                        Isi <code style="background:rgba(255,255,255,0.06);padding:0.1rem 0.35rem;border-radius:0.25rem;font-size:0.75rem;color:#94a3b8;">MIKROTIK_HOST</code>
                        di file <code style="background:rgba(255,255,255,0.06);padding:0.1rem 0.35rem;border-radius:0.25rem;font-size:0.75rem;color:#94a3b8;">.env</code>.
                    </p>

                @elseif ($connected)
                    <div class="flex items-center gap-2 mb-2">
                        <span style="display:inline-flex;align-items:center;gap:0.375rem;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#22c55e;font-size:0.8125rem;font-weight:500;padding:0.25rem 0.75rem;border-radius:9999px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;flex-shrink:0;display:inline-block;box-shadow:0 0 6px rgba(34,197,94,0.7);animation:nexaPulse 2s ease-in-out infinite;"></span>
                            Terhubung
                        </span>
                    </div>
                    <p style="font-size:0.8125rem;color:#64748b;margin:0 0 0.75rem;">Router merespons dengan normal.</p>
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:0.5rem;padding:0.625rem 0.875rem;">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="padding:0.2rem 0.75rem 0.2rem 0;color:#475569;font-size:0.75rem;">Host</td>
                                <td style="font-family:monospace;color:#cbd5e1;font-size:0.8rem;font-weight:500;">{{ $host }}</td>
                            </tr>
                            <tr>
                                <td style="padding:0.2rem 0.75rem 0.2rem 0;color:#475569;font-size:0.75rem;">Port</td>
                                <td style="font-family:monospace;color:#cbd5e1;font-size:0.8rem;font-weight:500;">{{ $port }}</td>
                            </tr>
                            <tr>
                                <td style="padding:0.2rem 0.75rem 0.2rem 0;color:#475569;font-size:0.75rem;">User</td>
                                <td style="font-family:monospace;color:#cbd5e1;font-size:0.8rem;font-weight:500;">{{ $user }}</td>
                            </tr>
                        </table>
                    </div>

                @else
                    <div class="flex items-center gap-2 mb-2">
                        <span style="display:inline-flex;align-items:center;gap:0.375rem;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171;font-size:0.8125rem;font-weight:500;padding:0.25rem 0.75rem;border-radius:9999px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#ef4444;flex-shrink:0;display:inline-block;"></span>
                            Tidak Terhubung
                        </span>
                    </div>
                    <p style="font-size:0.8125rem;color:#ef4444;opacity:0.75;margin:0;">
                        Router tidak dapat dihubungi. Periksa host, port, dan kredensial.
                    </p>
                @endif
            </div>

            {{-- SVG Router illustration --}}
            <div class="nexa-router-svg shrink-0 hidden sm:block">
                <svg width="90" height="80" viewBox="0 0 90 80" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    {{-- Antennas --}}
                    <line x1="28" y1="42" x2="18" y2="12" stroke="#22c55e" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                    <circle cx="18" cy="11" r="2.5" fill="#22c55e" opacity="0.7"/>
                    <line x1="45" y1="40" x2="45" y2="8" stroke="#22c55e" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                    <circle cx="45" cy="7" r="2.5" fill="#22c55e" opacity="0.7"/>
                    <line x1="62" y1="42" x2="72" y2="12" stroke="#22c55e" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                    <circle cx="72" cy="11" r="2.5" fill="#22c55e" opacity="0.7"/>

                    {{-- Router body --}}
                    <rect x="12" y="42" width="66" height="26" rx="5" ry="5"
                          fill="rgba(34,197,94,0.07)" stroke="rgba(34,197,94,0.4)" stroke-width="1.2"/>

                    {{-- Status LED dots --}}
                    @if ($connected)
                        <circle cx="24" cy="55" r="3" fill="#22c55e" opacity="0.9">
                            <animate attributeName="opacity" values="0.9;0.4;0.9" dur="2s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="33" cy="55" r="3" fill="#22c55e" opacity="0.7">
                            <animate attributeName="opacity" values="0.7;0.3;0.7" dur="2.4s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="42" cy="55" r="3" fill="#22c55e" opacity="0.6">
                            <animate attributeName="opacity" values="0.6;0.2;0.6" dur="1.8s" repeatCount="indefinite"/>
                        </circle>
                    @elseif (! $configured)
                        <circle cx="24" cy="55" r="3" fill="#64748b" opacity="0.6"/>
                        <circle cx="33" cy="55" r="3" fill="#64748b" opacity="0.4"/>
                        <circle cx="42" cy="55" r="3" fill="#64748b" opacity="0.3"/>
                    @else
                        <circle cx="24" cy="55" r="3" fill="#ef4444" opacity="0.8"/>
                        <circle cx="33" cy="55" r="3" fill="#ef4444" opacity="0.5"/>
                        <circle cx="42" cy="55" r="3" fill="#64748b" opacity="0.3"/>
                    @endif

                    {{-- Port slots --}}
                    <rect x="57" y="50" width="8" height="5" rx="1" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.12)" stroke-width="0.8"/>
                    <rect x="68" y="50" width="8" height="5" rx="1" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.12)" stroke-width="0.8"/>

                    {{-- Base / shadow --}}
                    <rect x="18" y="68" width="54" height="4" rx="2"
                          fill="rgba(34,197,94,0.06)" stroke="rgba(34,197,94,0.15)" stroke-width="0.8"/>

                    {{-- Wifi signal arcs (shown only when connected) --}}
                    @if ($connected)
                        <path d="M 34 28 Q 45 21 56 28" stroke="#22c55e" stroke-width="1.2" fill="none" opacity="0.45" stroke-linecap="round"/>
                        <path d="M 29 22 Q 45 13 61 22" stroke="#22c55e" stroke-width="1.2" fill="none" opacity="0.3" stroke-linecap="round"/>
                    @endif
                </svg>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
