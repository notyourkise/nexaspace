<div class="nxl-login">
    <style>
        :root {
            --nxl-bg: #06090d;
            --nxl-panel: rgba(12, 16, 22, 0.76);
            --nxl-line: rgba(255, 255, 255, 0.12);
            --nxl-line-soft: rgba(255, 255, 255, 0.07);
            --nxl-text: #f7fafc;
            --nxl-muted: #a7adba;
            --nxl-dim: #697386;
            --nxl-green: #4ade51;
            --nxl-green-bright: #65ff4d;
            --nxl-green-soft: rgba(74, 222, 81, 0.16);
        }

        html,
        body,
        body.fi-body {
            min-height: 100%;
            overflow-x: hidden;
            background: var(--nxl-bg) !important;
        }

        .nxl-login {
            min-height: 100vh;
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            background:
                radial-gradient(circle at 18% 24%, rgba(255, 255, 255, 0.045), transparent 20rem),
                radial-gradient(circle at 82% 16%, rgba(74, 222, 81, 0.15), transparent 26rem),
                linear-gradient(135deg, #070a0f 0%, #06090d 48%, #07140b 100%);
            color: var(--nxl-text);
            position: relative;
            overflow: hidden;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .nxl-login::before,
        .nxl-login::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }

        .nxl-login::before {
            width: 34rem;
            height: 34rem;
            right: -10rem;
            top: -12rem;
            border: 1px solid rgba(74, 222, 81, 0.28);
            background: radial-gradient(circle, rgba(74, 222, 81, 0.18), transparent 62%);
            opacity: 0.78;
        }

        .nxl-login::after {
            width: 28rem;
            height: 28rem;
            left: 42%;
            bottom: -15rem;
            border: 1px solid rgba(74, 222, 81, 0.26);
            background: radial-gradient(circle, rgba(74, 222, 81, 0.13), transparent 68%);
            opacity: 0.7;
        }

        .nxl-left,
        .nxl-right {
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }

        .nxl-left {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2rem, 6vw, 6rem);
            border-right: 1px solid var(--nxl-line);
            background:
                radial-gradient(circle at 43% 49%, rgba(74, 222, 81, 0.06), transparent 17rem),
                rgba(6, 9, 13, 0.56);
        }

        .nxl-form-shell {
            width: min(100%, 29rem);
            display: flex;
            min-height: min(47rem, calc(100vh - 5rem));
            flex-direction: column;
            justify-content: center;
        }

        .nxl-brand {
            text-align: center;
            margin-bottom: 3rem;
        }

        .nxl-brand img {
            display: block;
            width: 6.4rem;
            height: 6.4rem;
            object-fit: contain;
            margin: 0 auto 1.1rem;
            filter: drop-shadow(0 0 24px rgba(74, 222, 81, 0.25));
        }

        .nxl-brand h1 {
            margin: 0;
            color: #ffffff;
            font-size: 1.82rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .nxl-brand p {
            margin: 0.9rem 0 0;
            color: rgba(231, 236, 244, 0.78);
            font-size: 1rem;
        }

        .nxl-form {
            width: 100%;
        }

        .nxl-form .fi-sc,
        .nxl-form .fi-section,
        .nxl-form .fi-fo,
        .nxl-form form {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .nxl-form .fi-sc,
        .nxl-form .fi-fo {
            gap: 1.5rem !important;
        }

        .nxl-form .fi-fo-field-wrp {
            gap: 0.56rem !important;
        }

        .nxl-form .fi-fo-field-label,
        .nxl-form .fi-fo-field-label-content,
        .nxl-form .fi-fo-field-label-ctn,
        .nxl-form label {
            color: #ffffff !important;
            font-size: 0.98rem !important;
            font-weight: 650 !important;
        }

        .nxl-form .fi-fo-field-label-required-mark {
            color: #ff4f55 !important;
        }

        .nxl-form .fi-input-wrp,
        .nxl-form .fi-checkbox-input {
            background: rgba(13, 18, 26, 0.82) !important;
            border: 1px solid rgba(255, 255, 255, 0.14) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.035), 0 0 0 0 rgba(74, 222, 81, 0) !important;
        }

        .nxl-form .fi-input-wrp {
            min-height: 3.32rem !important;
            border-radius: 0.55rem !important;
            overflow: hidden;
        }

        .nxl-form .fi-input-wrp:focus-within {
            border-color: rgba(74, 222, 81, 0.76) !important;
            box-shadow: 0 0 0 1px rgba(74, 222, 81, 0.25), 0 0 28px rgba(74, 222, 81, 0.14) !important;
        }

        .nxl-form input,
        .nxl-form .fi-input {
            color: #ffffff !important;
            background: transparent !important;
            font-size: 0.98rem !important;
        }

        .nxl-form input::placeholder {
            color: rgba(167, 173, 186, 0.72) !important;
        }

        .nxl-form .fi-icon-btn {
            color: rgba(232, 238, 247, 0.72) !important;
        }

        .nxl-form .fi-checkbox-input {
            width: 1.2rem !important;
            height: 1.2rem !important;
            border-radius: 0.28rem !important;
        }

        .nxl-form .fi-checkbox-input:checked {
            background-color: var(--nxl-green) !important;
            border-color: var(--nxl-green) !important;
        }

        .nxl-form .fi-btn,
        .nxl-form button[type="submit"] {
            min-height: 3.36rem !important;
            width: 100% !important;
            border-radius: 0.52rem !important;
            border: 1px solid rgba(101, 255, 77, 0.56) !important;
            background: linear-gradient(180deg, rgba(74, 222, 81, 0.58), rgba(38, 122, 45, 0.8)) !important;
            color: #ffffff !important;
            font-size: 0.98rem !important;
            font-weight: 760 !important;
            box-shadow: 0 0 24px rgba(74, 222, 81, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
        }

        .nxl-form .fi-btn:hover,
        .nxl-form button[type="submit"]:hover {
            background: linear-gradient(180deg, rgba(101, 255, 77, 0.68), rgba(43, 143, 52, 0.86)) !important;
        }

        .nxl-copy {
            margin-top: auto;
            padding-top: 4.5rem;
            text-align: center;
            color: rgba(167, 173, 186, 0.82);
            font-size: 0.92rem;
        }

        .nxl-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2.5rem, 6vw, 6.5rem);
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(5, 8, 12, 0.12), rgba(9, 34, 14, 0.42)),
                radial-gradient(circle at 58% 54%, rgba(74, 222, 81, 0.16), transparent 18rem);
        }

        .nxl-right::before {
            content: "";
            position: absolute;
            inset: auto -5rem -10rem auto;
            width: 38rem;
            height: 17rem;
            opacity: 0.42;
            background-image: radial-gradient(circle, rgba(101, 255, 77, 0.54) 1px, transparent 1.5px);
            background-size: 0.82rem 0.82rem;
            transform: rotate(-8deg);
            mask-image: radial-gradient(ellipse at center, black, transparent 68%);
            -webkit-mask-image: radial-gradient(ellipse at center, black, transparent 68%);
        }

        .nxl-right::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(47deg, transparent 0 49.85%, rgba(74, 222, 81, 0.08) 50%, transparent 50.15%),
                linear-gradient(132deg, transparent 0 49.85%, rgba(255, 255, 255, 0.035) 50%, transparent 50.15%);
            pointer-events: none;
        }

        .nxl-showcase {
            position: relative;
            z-index: 1;
            width: min(100%, 45rem);
            text-align: center;
        }

        .nxl-home-icon {
            position: relative;
            display: grid;
            place-items: center;
            width: 8.55rem;
            height: 8.55rem;
            margin: 0 auto 1.75rem;
            border-radius: 1.45rem;
            border: 1px solid rgba(74, 222, 81, 0.35);
            background: rgba(255, 255, 255, 0.045);
            box-shadow: 0 0 44px rgba(74, 222, 81, 0.17);
        }

        .nxl-home-icon svg {
            width: 4.4rem;
            height: 4.4rem;
            color: rgba(133, 246, 142, 0.94);
            filter: drop-shadow(0 0 18px rgba(74, 222, 81, 0.32));
        }

        .nxl-showcase h2 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(2.2rem, 4vw, 3.1rem);
            font-weight: 820;
            letter-spacing: -0.04em;
            text-shadow: 0 12px 30px rgba(0, 0, 0, 0.38);
        }

        .nxl-showcase .nxl-subtitle {
            position: relative;
            display: inline-block;
            margin-top: 1.15rem;
            color: var(--nxl-green);
            font-size: clamp(1.18rem, 2vw, 1.52rem);
            font-weight: 620;
            text-shadow: 0 0 24px rgba(74, 222, 81, 0.42);
        }

        .nxl-showcase .nxl-subtitle::after {
            content: "";
            position: absolute;
            left: 28%;
            right: 28%;
            bottom: -1rem;
            height: 0.13rem;
            background: var(--nxl-green);
            border-radius: 999px;
            box-shadow: 0 0 24px rgba(74, 222, 81, 0.85);
        }

        .nxl-features {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.45rem;
            margin-top: 4.55rem;
            text-align: left;
        }

        .nxl-feature {
            display: flex;
            align-items: center;
            gap: 1.12rem;
            min-height: 5.65rem;
            padding: 1.1rem 1.25rem;
            border-radius: 0.8rem;
            border: 1px solid var(--nxl-line);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.025));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05), 0 18px 44px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(16px);
        }

        .nxl-feature svg {
            width: 2.05rem;
            height: 2.05rem;
            flex: 0 0 auto;
            color: var(--nxl-green);
            filter: drop-shadow(0 0 14px rgba(74, 222, 81, 0.32));
        }

        .nxl-feature strong {
            display: block;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 780;
            line-height: 1.25;
        }

        .nxl-feature span {
            display: block;
            margin-top: 0.35rem;
            color: rgba(231, 236, 244, 0.76);
            font-size: 0.9rem;
            line-height: 1.3;
        }

        @media (max-width: 1024px) {
            .nxl-login {
                grid-template-columns: 1fr;
            }

            .nxl-left {
                border-right: 0;
                border-bottom: 1px solid var(--nxl-line);
            }

            .nxl-right {
                min-height: auto;
            }
        }

        @media (max-width: 640px) {
            .nxl-left,
            .nxl-right {
                padding: 2rem 1.25rem;
            }

            .nxl-form-shell {
                min-height: auto;
            }

            .nxl-brand {
                margin-bottom: 2rem;
            }

            .nxl-brand img {
                width: 5rem;
                height: 5rem;
            }

            .nxl-features {
                grid-template-columns: 1fr;
                margin-top: 3rem;
            }

            .nxl-copy {
                padding-top: 2.5rem;
            }
        }
    </style>

    <section class="nxl-left">
        <div class="nxl-form-shell">
            <div class="nxl-brand">
                <img src="{{ asset('images/logo-nexa.png') }}" alt="NexaSpace">
                <h1>NexaSpace</h1>
                <p>Admin Panel</p>
            </div>

            <div class="nxl-form">
                {{ $this->content }}
            </div>

            <p class="nxl-copy">© 2026 NexaSpace. All rights reserved.</p>

            <x-filament-actions::modals />
        </div>
    </section>

    <section class="nxl-right" aria-hidden="true">
        <div class="nxl-showcase">
            <div class="nxl-home-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.35" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.25 12 3l9 7.25V21a.75.75 0 0 1-.75.75H15.25a.75.75 0 0 1-.75-.75v-5.25h-5V21a.75.75 0 0 1-.75.75h-5A.75.75 0 0 1 3 21V10.25Z"/>
                </svg>
            </div>

            <h2>Smart Boarding House</h2>
            <p class="nxl-subtitle">Auto-Throttle WiFi Billing System</p>

            <div class="nxl-features">
                <div class="nxl-feature">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <div>
                        <strong>Automated throttling</strong>
                        <span>Smart bandwidth control</span>
                    </div>
                </div>

                <div class="nxl-feature">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>
                    </svg>
                    <div>
                        <strong>Real-time MikroTik sync</strong>
                        <span>Instant router synchronization</span>
                    </div>
                </div>

                <div class="nxl-feature">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15A2.25 2.25 0 0 0 21.75 17.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                    </svg>
                    <div>
                        <strong>Manual payments</strong>
                        <span>Bank transfer and receipt proof</span>
                    </div>
                </div>

                <div class="nxl-feature">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.12a7.5 7.5 0 0 1 15 0A17.93 17.93 0 0 1 12 21.75c-2.68 0-5.22-.58-7.5-1.63Z"/>
                    </svg>
                    <div>
                        <strong>Tenant self-service</strong>
                        <span>Empower your tenants</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
