{{-- Single Livewire root element — fills the full viewport now that fi-simple-main is bypassed --}}
<div style="display:flex;min-height:100vh;width:100%;background:#ffffff;">

    {{-- ═══════════════════════════════════════════════════
         LEFT — Login form
    ═══════════════════════════════════════════════════ --}}
    <div style="flex:0 0 50%;display:flex;align-items:center;justify-content:center;padding:2.5rem;overflow-y:auto;background:#ffffff;">
        <div style="width:100%;max-width:22rem;">

            {{-- Brand --}}
            <div style="text-align:center;margin-bottom:2.5rem;">
                <img src="{{ asset('images/logo-nexa.png') }}"
                     alt="NexaSpace"
                     style="height:4rem;width:auto;margin:0 auto 1rem;display:block;object-fit:contain;">
                <div style="font-size:1.5rem;font-weight:700;color:#111827;letter-spacing:-0.025em;">NexaSpace</div>
                <div style="font-size:0.875rem;color:#6b7280;margin-top:0.25rem;">Portal Anak Kos</div>
            </div>

            {{ $this->content }}

            <x-filament-actions::modals />
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         RIGHT — Abstract PropTech illustration
    ═══════════════════════════════════════════════════ --}}
    <div style="flex:0 0 50%;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;padding:3rem;background:linear-gradient(150deg,#f5f0d4 0%,#E7E1B1 45%,#d6cf98 100%);">

        {{-- Ambient blobs --}}
        <div style="position:absolute;top:-9rem;right:-9rem;height:26rem;width:26rem;border-radius:9999px;background:#306D29;opacity:0.09;"></div>
        <div style="position:absolute;bottom:-6rem;left:-6rem;height:20rem;width:20rem;border-radius:9999px;background:#0D530E;opacity:0.07;"></div>

        <div style="position:relative;z-index:10;text-align:center;max-width:30rem;">

            {{-- Abstract geometric illustration card --}}
            <div style="margin:0 auto 2.25rem;display:flex;align-items:center;justify-content:center;height:15rem;width:15rem;border-radius:2rem;background:#ffffff;box-shadow:0 12px 48px rgba(0,0,0,0.13);">

                <svg viewBox="0 0 280 280" xmlns="http://www.w3.org/2000/svg"
                     style="width:12rem;height:12rem;">

                    <!-- Outer orbital ring -->
                    <circle cx="140" cy="140" r="122" fill="none" stroke="#E7E1B1" stroke-width="1" opacity="0.7"/>

                    <!-- Outer network nodes -->
                    <circle cx="140" cy="18"  r="7"   fill="#306D29"/>
                    <circle cx="140" cy="18"  r="12"  fill="none" stroke="#306D29" stroke-width="1" opacity="0.35"/>
                    <circle cx="55"  cy="58"  r="5"   fill="#0D530E"/>
                    <circle cx="55"  cy="58"  r="9"   fill="none" stroke="#0D530E" stroke-width="1" opacity="0.3"/>
                    <circle cx="225" cy="58"  r="5"   fill="#0D530E"/>
                    <circle cx="225" cy="58"  r="9"   fill="none" stroke="#0D530E" stroke-width="1" opacity="0.3"/>
                    <circle cx="16"  cy="148" r="4.5" fill="#306D29" opacity="0.8"/>
                    <circle cx="264" cy="148" r="4.5" fill="#306D29" opacity="0.8"/>
                    <circle cx="55"  cy="232" r="4"   fill="#0D530E" opacity="0.7"/>
                    <circle cx="225" cy="232" r="4"   fill="#0D530E" opacity="0.7"/>
                    <circle cx="140" cy="262" r="5"   fill="#306D29" opacity="0.6"/>

                    <!-- Network connection lines -->
                    <line x1="140" y1="30"  x2="88"  y2="90"  stroke="#306D29" stroke-width="1.2" opacity="0.25"/>
                    <line x1="140" y1="30"  x2="192" y2="90"  stroke="#306D29" stroke-width="1.2" opacity="0.25"/>
                    <line x1="55"  y1="67"  x2="30"  y2="140" stroke="#0D530E" stroke-width="1"   opacity="0.2"/>
                    <line x1="55"  y1="67"  x2="88"  y2="90"  stroke="#0D530E" stroke-width="1"   opacity="0.2" stroke-dasharray="5,3"/>
                    <line x1="225" y1="67"  x2="252" y2="140" stroke="#0D530E" stroke-width="1"   opacity="0.2"/>
                    <line x1="225" y1="67"  x2="192" y2="90"  stroke="#0D530E" stroke-width="1"   opacity="0.2" stroke-dasharray="5,3"/>
                    <line x1="30"  y1="148" x2="88"  y2="170" stroke="#306D29" stroke-width="1"   opacity="0.2" stroke-dasharray="4,4"/>
                    <line x1="252" y1="148" x2="192" y2="170" stroke="#306D29" stroke-width="1"   opacity="0.2" stroke-dasharray="4,4"/>
                    <line x1="55"  y1="224" x2="88"  y2="192" stroke="#0D530E" stroke-width="1"   opacity="0.2"/>
                    <line x1="225" y1="224" x2="192" y2="192" stroke="#0D530E" stroke-width="1"   opacity="0.2"/>
                    <line x1="140" y1="250" x2="88"  y2="192" stroke="#306D29" stroke-width="1"   opacity="0.18" stroke-dasharray="3,4"/>
                    <line x1="140" y1="250" x2="192" y2="192" stroke="#306D29" stroke-width="1"   opacity="0.18" stroke-dasharray="3,4"/>

                    <!-- Main isometric building: top face -->
                    <polygon points="140,82 200,114 140,146 80,114" fill="#306D29"/>
                    <!-- Left face -->
                    <polygon points="80,114 140,146 140,210 80,178"  fill="#0D530E"/>
                    <!-- Right face -->
                    <polygon points="200,114 140,146 140,210 200,178" fill="#1e5c1a"/>

                    <!-- Top face grid detail -->
                    <line x1="140" y1="82"  x2="140" y2="146" stroke="rgba(255,255,255,0.18)" stroke-width="0.8"/>
                    <line x1="80"  y1="114" x2="200" y2="114" stroke="rgba(255,255,255,0.12)" stroke-width="0.8"/>
                    <line x1="110" y1="98"  x2="170" y2="130" stroke="rgba(255,255,255,0.10)" stroke-width="0.7"/>
                    <line x1="170" y1="98"  x2="110" y2="130" stroke="rgba(255,255,255,0.10)" stroke-width="0.7"/>

                    <!-- Right face window -->
                    <rect x="153" y="158" width="18" height="14" rx="1"
                          fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.25)" stroke-width="0.8"/>
                    <!-- Right face door -->
                    <rect x="173" y="176" width="12" height="18" rx="1"
                          fill="rgba(0,0,0,0.18)" stroke="rgba(255,255,255,0.2)" stroke-width="0.8"/>
                    <!-- Vertical ridge -->
                    <line x1="140" y1="146" x2="140" y2="210" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>

                    <!-- Antenna mast -->
                    <line x1="140" y1="82" x2="140" y2="50" stroke="#306D29" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="140" cy="47" r="3" fill="#306D29"/>

                    <!-- WiFi arcs -->
                    <path d="M 125,68 Q 140,55 155,68" fill="none" stroke="#306D29" stroke-width="2.2" stroke-linecap="round" opacity="0.9"/>
                    <path d="M 116,76 Q 140,58 164,76" fill="none" stroke="#306D29" stroke-width="1.6" stroke-linecap="round" opacity="0.55"/>
                    <path d="M 107,84 Q 140,61 173,84" fill="none" stroke="#306D29" stroke-width="1.2" stroke-linecap="round" opacity="0.28"/>

                    <!-- Satellite building right -->
                    <polygon points="200,148 230,164 200,180 170,164" fill="#306D29" opacity="0.55"/>
                    <polygon points="170,164 200,180 200,216 170,200" fill="#0D530E"  opacity="0.55"/>
                    <polygon points="230,164 200,180 200,216 230,200" fill="#1e5c1a"  opacity="0.55"/>

                    <!-- Satellite building left -->
                    <polygon points="80,152 110,168 80,184 50,168"  fill="#306D29" opacity="0.45"/>
                    <polygon points="50,168 80,184 80,218 50,202"   fill="#0D530E"  opacity="0.45"/>
                    <polygon points="110,168 80,184 80,218 110,202" fill="#1e5c1a"  opacity="0.45"/>

                    <!-- Mid-layer mesh nodes -->
                    <circle cx="88"  cy="90"  r="4" fill="#E7E1B1" stroke="#306D29" stroke-width="1.5"/>
                    <circle cx="192" cy="90"  r="4" fill="#E7E1B1" stroke="#306D29" stroke-width="1.5"/>
                    <circle cx="88"  cy="192" r="4" fill="#E7E1B1" stroke="#0D530E" stroke-width="1.5"/>
                    <circle cx="192" cy="192" r="4" fill="#E7E1B1" stroke="#0D530E" stroke-width="1.5"/>
                    <line x1="88"  y1="90"  x2="192" y2="90"  stroke="#306D29" stroke-width="0.8" opacity="0.2"/>
                    <line x1="88"  y1="192" x2="192" y2="192" stroke="#0D530E" stroke-width="0.8" opacity="0.2"/>
                    <line x1="88"  y1="90"  x2="88"  y2="192" stroke="#306D29" stroke-width="0.8" opacity="0.15"/>
                    <line x1="192" y1="90"  x2="192" y2="192" stroke="#0D530E" stroke-width="0.8" opacity="0.15"/>

                    <!-- Data flow dots -->
                    <circle cx="140" cy="65"  r="2"   fill="#E7E1B1" opacity="0.8"/>
                    <circle cx="104" cy="100" r="1.5" fill="#E7E1B1" opacity="0.6"/>
                    <circle cx="176" cy="100" r="1.5" fill="#E7E1B1" opacity="0.6"/>
                    <circle cx="60"  cy="145" r="1.5" fill="#306D29" opacity="0.5"/>
                    <circle cx="220" cy="145" r="1.5" fill="#306D29" opacity="0.5"/>
                    <circle cx="140" cy="225" r="2"   fill="#0D530E" opacity="0.4"/>

                </svg>
            </div>

            <div style="font-size:1.875rem;font-weight:700;color:#1a3d18;line-height:1.25;">Welcome Home</div>
            <div style="margin-top:0.875rem;font-size:1rem;color:#4a6b48;line-height:1.6;">
                Manage your bills and payments<br>from anywhere, anytime.
            </div>
        </div>
    </div>

</div>
