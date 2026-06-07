<div class="ntl-login">
    <style>
        :root {
            --ntl-bg: #04070a;
            --ntl-panel: rgba(7, 10, 14, 0.92);
            --ntl-text: #ffffff;
            --ntl-muted: rgba(255, 255, 255, 0.72);
            --ntl-soft: rgba(255, 255, 255, 0.1);
            --ntl-green: #5ee66b;
            --ntl-green-bright: #78ff72;
            --ntl-green-dark: #0c2f17;
        }

        html,
        body,
        body.fi-body {
            min-height: 100%;
            overflow-x: hidden;
            background: var(--ntl-bg) !important;
        }

        .ntl-login {
            min-height: 100vh;
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1.07fr) minmax(0, 0.93fr);
            color: var(--ntl-text);
            background:
                radial-gradient(circle at 18% 58%, rgba(94, 230, 107, 0.18), transparent 20rem),
                radial-gradient(circle at 85% 16%, rgba(255, 255, 255, 0.04), transparent 20rem),
                linear-gradient(135deg, #030609 0%, #07100c 48%, #04070a 100%);
            position: relative;
            overflow: hidden;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .ntl-login::before,
        .ntl-login::after {
            content: "";
            position: absolute;
            pointer-events: none;
            border-radius: 999px;
        }

        .ntl-login::before {
            width: 22rem;
            height: 22rem;
            left: 35%;
            top: 26%;
            border: 1px solid rgba(94, 230, 107, 0.18);
            opacity: 0.5;
        }

        .ntl-login::after {
            width: 42rem;
            height: 18rem;
            left: -5rem;
            bottom: 6rem;
            background-image: repeating-radial-gradient(ellipse at center, transparent 0 1.05rem, rgba(94, 230, 107, 0.24) 1.1rem 1.14rem, transparent 1.2rem 1.85rem);
            transform: rotate(8deg);
            opacity: 0.34;
            mask-image: linear-gradient(90deg, black 0%, black 58%, transparent 100%);
            -webkit-mask-image: linear-gradient(90deg, black 0%, black 58%, transparent 100%);
        }

        .ntl-left,
        .ntl-right {
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }

        .ntl-left {
            display: flex;
            align-items: center;
            padding: clamp(3rem, 7vw, 6.8rem);
            overflow: hidden;
        }

        .ntl-left::before,
        .ntl-left::after,
        .ntl-right::before,
        .ntl-right::after {
            content: "";
            position: absolute;
            pointer-events: none;
        }

        .ntl-left::before {
            width: 5.6rem;
            height: 5.6rem;
            right: 24%;
            top: 26%;
            border-radius: 999px;
            border: 1px solid rgba(94, 230, 107, 0.24);
            background: rgba(94, 230, 107, 0.035);
        }

        .ntl-left::after {
            right: 13%;
            top: 17%;
            width: 7.5rem;
            height: 4.5rem;
            background-image: radial-gradient(circle, rgba(94, 230, 107, 0.28) 1.5px, transparent 1.7px);
            background-size: 1rem 1rem;
            opacity: 0.28;
        }

        .ntl-left-inner {
            width: min(100%, 42rem);
        }

        .ntl-wordmark {
            display: inline-flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: clamp(4rem, 12vh, 8rem);
        }

        .ntl-wordmark img {
            width: 3.1rem;
            height: 3.1rem;
            object-fit: contain;
            filter: drop-shadow(0 0 18px rgba(94, 230, 107, 0.28));
        }

        .ntl-wordmark span {
            color: #ffffff;
            font-size: clamp(1.35rem, 2vw, 1.75rem);
            font-weight: 820;
            letter-spacing: -0.03em;
        }

        .ntl-hero h1 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(4.8rem, 7.2vw, 6.8rem);
            line-height: 0.98;
            font-weight: 900;
            letter-spacing: -0.055em;
            text-shadow: 0 18px 36px rgba(0, 0, 0, 0.42);
        }

        .ntl-hero h1 strong {
            display: block;
            color: #70f27a;
            font-weight: 900;
            text-shadow: 0 0 22px rgba(94, 230, 107, 0.62), 0 18px 36px rgba(0, 0, 0, 0.42);
        }

        .ntl-hero p {
            margin: 2.2rem 0 0;
            max-width: 33rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: clamp(1.35rem, 2.1vw, 1.82rem);
            line-height: 1.55;
            font-weight: 520;
        }

        .ntl-hero-mark {
            display: block;
            width: 4.4rem;
            height: 0.22rem;
            margin-top: 2.4rem;
            border-radius: 999px;
            background: var(--ntl-green);
            box-shadow: 0 0 20px rgba(94, 230, 107, 0.7);
        }

        .ntl-divider {
            position: absolute;
            inset: 0 auto 0 53.5%;
            z-index: 2;
            width: 1px;
            background: linear-gradient(180deg, rgba(94, 230, 107, 0.2), rgba(94, 230, 107, 0.9), rgba(94, 230, 107, 0.24));
            box-shadow: 0 0 16px rgba(94, 230, 107, 0.86);
        }

        .ntl-divider-badge {
            position: absolute;
            top: 50%;
            left: 53.5%;
            z-index: 3;
            display: grid;
            place-items: center;
            width: 3.35rem;
            height: 3.35rem;
            border-radius: 999px;
            transform: translate(-50%, -50%);
            color: #70f27a;
            border: 1px solid rgba(112, 242, 122, 0.9);
            background: #091111;
            box-shadow: 0 0 24px rgba(94, 230, 107, 0.54), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            font-size: 1.42rem;
            font-weight: 900;
        }

        .ntl-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2.25rem, 6vw, 6rem);
            background:
                radial-gradient(circle at 48% 50%, rgba(255, 255, 255, 0.035), transparent 18rem),
                linear-gradient(135deg, rgba(255, 255, 255, 0.018), rgba(0, 0, 0, 0.2));
        }

        .ntl-right::before {
            right: 8%;
            bottom: 10%;
            width: 7.5rem;
            height: 4.8rem;
            background-image: radial-gradient(circle, rgba(94, 230, 107, 0.35) 1.5px, transparent 1.8px);
            background-size: 1rem 1rem;
            opacity: 0.24;
        }

        .ntl-right::after {
            inset: 0;
            background: linear-gradient(42deg, transparent 0 49.9%, rgba(94, 230, 107, 0.055) 50%, transparent 50.1%);
        }

        .ntl-form-shell {
            position: relative;
            z-index: 1;
            width: min(100%, 32rem);
        }

        .ntl-brand {
            text-align: center;
            margin-bottom: 2.8rem;
        }

        .ntl-brand img {
            display: block;
            width: 6rem;
            height: 6rem;
            object-fit: contain;
            margin: 0 auto 1.15rem;
            filter: drop-shadow(0 0 24px rgba(94, 230, 107, 0.28));
        }

        .ntl-brand h2 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(2rem, 3vw, 2.6rem);
            line-height: 1;
            font-weight: 860;
            letter-spacing: -0.04em;
        }

        .ntl-brand p {
            margin: 0.95rem 0 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.05rem;
        }

        .ntl-form .fi-sc,
        .ntl-form .fi-section,
        .ntl-form .fi-fo,
        .ntl-form form {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .ntl-form .fi-sc,
        .ntl-form .fi-fo {
            gap: 1.55rem !important;
        }

        .ntl-form .fi-fo-field-wrp {
            gap: 0.62rem !important;
        }

        .ntl-form .fi-fo-field-label,
        .ntl-form .fi-fo-field-label-content,
        .ntl-form .fi-fo-field-label-ctn,
        .ntl-form label {
            color: #ffffff !important;
            font-size: 1rem !important;
            font-weight: 760 !important;
        }

        .ntl-form .fi-fo-field-label-required-mark {
            color: #ff4f55 !important;
        }

        .ntl-form .fi-input-wrp {
            min-height: 3.55rem !important;
            overflow: hidden;
            border-radius: 0.62rem !important;
            border: 1px solid rgba(255, 255, 255, 0.16) !important;
            background: rgba(12, 16, 23, 0.86) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04), 0 0 0 0 rgba(94, 230, 107, 0) !important;
        }

        .ntl-form .fi-input-wrp:focus-within {
            border-color: rgba(94, 230, 107, 0.78) !important;
            box-shadow: 0 0 0 1px rgba(94, 230, 107, 0.22), 0 0 26px rgba(94, 230, 107, 0.16) !important;
        }

        .ntl-form .fi-input,
        .ntl-form input {
            color: #ffffff !important;
            background: transparent !important;
            font-size: 1rem !important;
            font-weight: 520 !important;
        }

        .ntl-form input::placeholder {
            color: rgba(255, 255, 255, 0.68) !important;
        }

        .ntl-form .fi-input-wrp-prefix,
        .ntl-form .fi-input-wrp-suffix,
        .ntl-form .fi-icon,
        .ntl-form .fi-icon-btn {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .ntl-form .fi-checkbox-input {
            width: 1.22rem !important;
            height: 1.22rem !important;
            border-radius: 0.28rem !important;
            border: 1px solid rgba(94, 230, 107, 0.58) !important;
            background: rgba(7, 16, 10, 0.86) !important;
            color: var(--ntl-green) !important;
        }

        .ntl-form .fi-checkbox-input:checked {
            background-color: #1ea83b !important;
            border-color: #67f26f !important;
        }

        .ntl-form .fi-checkbox-input:checked + label,
        .ntl-form label:has(.fi-checkbox-input:checked) {
            color: #ffffff !important;
        }

        .ntl-form .fi-btn,
        .ntl-form button[type="submit"] {
            min-height: 3.66rem !important;
            width: 100% !important;
            border-radius: 0.6rem !important;
            border: 1px solid rgba(121, 255, 128, 0.58) !important;
            background: linear-gradient(180deg, #68e874 0%, #51d864 100%) !important;
            color: #05210b !important;
            font-size: 1.06rem !important;
            font-weight: 860 !important;
            box-shadow: 0 12px 34px rgba(94, 230, 107, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.34) !important;
        }

        .ntl-form .fi-btn:hover,
        .ntl-form button[type="submit"]:hover {
            background: linear-gradient(180deg, #7cff86 0%, #55de68 100%) !important;
            box-shadow: 0 16px 40px rgba(94, 230, 107, 0.34), inset 0 1px 0 rgba(255, 255, 255, 0.38) !important;
        }

        .ntl-form .fi-btn *,
        .ntl-form button[type="submit"] * {
            color: #05210b !important;
            font-weight: 860 !important;
        }

        .ntl-form button[type="submit"] .fi-btn-label::after {
            content: " \2192";
            display: inline-block;
            margin-left: 0.7rem;
            font-size: 1.25rem;
            transform: translateY(0.02rem);
        }

        .ntl-help {
            display: grid;
            gap: 1.75rem;
            margin-top: 2.65rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }

        .ntl-or {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 1.5rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .ntl-or::before,
        .ntl-or::after {
            content: "";
            height: 1px;
            background: rgba(255, 255, 255, 0.09);
        }

        .ntl-help a {
            color: #70f27a;
            text-decoration: none;
            font-weight: 760;
        }

        .ntl-copy {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            margin-top: 1.9rem;
            color: rgba(255, 255, 255, 0.58);
            font-size: 0.95rem;
        }

        .ntl-copy svg {
            width: 1.1rem;
            height: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 1024px) {
            .ntl-login {
                grid-template-columns: 1fr;
            }

            .ntl-left,
            .ntl-right {
                min-height: auto;
                padding: 3rem 1.5rem;
            }

            .ntl-left {
                min-height: 46vh;
            }

            .ntl-divider,
            .ntl-divider-badge {
                display: none;
            }

            .ntl-wordmark {
                margin-bottom: 3.5rem;
            }
        }

        @media (max-width: 640px) {
            .ntl-left,
            .ntl-right {
                padding: 2.25rem 1.25rem;
            }

            .ntl-wordmark {
                margin-bottom: 3rem;
            }

            .ntl-wordmark img {
                width: 2.5rem;
                height: 2.5rem;
            }

            .ntl-hero h1 {
                font-size: 4rem;
            }

            .ntl-hero p {
                font-size: 1.15rem;
            }

            .ntl-brand {
                margin-bottom: 2.2rem;
            }

            .ntl-brand img {
                width: 5rem;
                height: 5rem;
            }
        }
    </style>

    <section class="ntl-left" aria-hidden="true">
        <div class="ntl-left-inner">
            <div class="ntl-wordmark">
                <img src="{{ asset('images/logo-nexa.png') }}" alt="">
                <span>NexaSpace</span>
            </div>

            <div class="ntl-hero">
                <h1>
                    Welcome
                    <strong>Home</strong>
                </h1>

                <p>Kelola tagihan dan pembayaran Anda dengan mudah kapan saja.</p>

                <span class="ntl-hero-mark"></span>
            </div>
        </div>
    </section>

    <div class="ntl-divider"></div>
    <div class="ntl-divider-badge">N</div>

    <section class="ntl-right">
        <div class="ntl-form-shell">
            <div class="ntl-brand">
                <img src="{{ asset('images/logo-nexa.png') }}" alt="NexaSpace">
                <h2>NexaSpace</h2>
                <p>Portal Anak Kos</p>
            </div>

            <div class="ntl-form">
                {{ $this->content }}
            </div>

            <div class="ntl-help">
                <div class="ntl-or">atau</div>
                <div>Butuh bantuan? Hubungi <a href="mailto:support@nexaspace.site">pengelola kos Anda.</a></div>

                <div class="ntl-copy">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 19.5 6.5v5.4c0 4.38-2.94 7.93-7.5 9.35-4.56-1.42-7.5-4.97-7.5-9.35V6.5L12 3.75Z" />
                    </svg>
                    <span>&copy; 2026 NexaSpace. All rights reserved.</span>
                </div>
            </div>

            <x-filament-actions::modals />
        </div>
    </section>
</div>
