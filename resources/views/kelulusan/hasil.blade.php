<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Hasil Kelulusan — SDN Tomang 03 Pagi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F4E4C1;
            --gold-deep: #B8941F;
            --navy-deep: #0A1628;
            --navy-primary: #111D35;
            --navy-light: #1A2942;
            --navy-card: #141F38;
            --cream: #FFFEF9;
            --cream-dark: #F5F3E8;
            --accent-blue: #4A90E2;
            --accent-green: #10B981;
            --accent-red: #DC2626;
            --text-muted: rgba(255, 255, 255, 0.5);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 8px 32px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 16px 64px rgba(0, 0, 0, 0.3);
            --shadow-xl: 0 32px 96px rgba(0, 0, 0, 0.4);
        }

        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        html,
        body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--navy-deep);
            color: #fff;
            min-height: 100dvh;
            position: relative;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-scene::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 100% 80% at 20% 0%, rgba(212, 175, 55, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse 90% 70% at 80% 100%, rgba(74, 144, 226, 0.06) 0%, transparent 50%),
                linear-gradient(135deg, #0A1628 0%, #141F38 40%, #0F1A2E 70%, #0A1628 100%);
        }

        .bg-scene::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            opacity: 0.5;
            pointer-events: none;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            mix-blend-mode: screen;
        }

        .orb-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.12) 0%, rgba(212, 175, 55, 0.04) 40%, transparent 70%);
            top: -150px;
            left: -150px;
            animation: orbFloat1 20s ease-in-out infinite;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(74, 144, 226, 0.1) 0%, rgba(74, 144, 226, 0.03) 40%, transparent 70%);
            bottom: -100px;
            right: -100px;
            animation: orbFloat2 25s ease-in-out infinite;
        }

        .orb-3 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(184, 148, 31, 0.08) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: orbFloat3 30s ease-in-out infinite;
        }

        @keyframes orbFloat1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }

            25% {
                transform: translate(50px, -30px) scale(1.1);
                opacity: 0.8;
            }

            50% {
                transform: translate(20px, 40px) scale(0.9);
                opacity: 1;
            }

            75% {
                transform: translate(-30px, 20px) scale(1.05);
                opacity: 0.9;
            }
        }

        @keyframes orbFloat2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }

            33% {
                transform: translate(-40px, 30px) scale(1.08);
                opacity: 0.85;
            }

            66% {
                transform: translate(25px, -20px) scale(0.92);
                opacity: 1;
            }
        }

        @keyframes orbFloat3 {

            0%,
            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.6;
            }

            50% {
                transform: translate(-50%, -50%) scale(1.15);
                opacity: 0.8;
            }
        }

        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(212, 175, 55, 0.03) 1.5px, transparent 1.5px),
                linear-gradient(90deg, rgba(212, 175, 55, 0.03) 1.5px, transparent 1.5px);
            background-size: 80px 80px;
            mask-image: radial-gradient(ellipse 90% 90% at 50% 50%, black 20%, transparent 100%);
            animation: gridPulse 8s ease-in-out infinite;
        }

        @keyframes gridPulse {

            0%,
            100% {
                opacity: 0.4;
            }

            50% {
                opacity: 0.7;
            }
        }

        .light-rays {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(45deg, transparent 48%, rgba(212, 175, 55, 0.02) 50%, transparent 52%),
                linear-gradient(-45deg, transparent 48%, rgba(212, 175, 55, 0.02) 50%, transparent 52%);
            background-size: 200px 200px;
            animation: raysMove 40s linear infinite;
            opacity: 0.3;
        }

        @keyframes raysMove {
            0% {
                background-position: 0 0, 0 0;
            }

            100% {
                background-position: 200px 200px, -200px -200px;
            }
        }

        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: var(--gold-primary);
            border-radius: 50%;
            opacity: 0;
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.5);
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.8;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-10vh) translateX(var(--drift, 40px)) rotate(360deg);
                opacity: 0;
            }
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
        }

        .modal-panel {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 520px;
            background: var(--cream);
            border-radius: 32px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.5),
                0 24px 96px rgba(0, 0, 0, 0.6),
                0 48px 128px rgba(0, 0, 0, 0.4);
            transition: all 0.4s ease;
        }

        .state-loading {
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: linear-gradient(135deg, #0A1628 0%, #141F38 100%);
            color: #fff;
            position: relative;
        }

        .state-loading::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 30%, rgba(212, 175, 55, 0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        .scanner {
            width: 120px;
            height: 120px;
            position: relative;
            margin-bottom: 2.5rem;
        }

        .scanner-ring {
            position: absolute;
            inset: 0;
            border: 2.5px solid rgba(212, 175, 55, 0.15);
            border-radius: 50%;
        }

        .scanner-ring-spin {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2.5px solid transparent;
            border-top-color: var(--gold-primary);
            border-right-color: rgba(212, 175, 55, 0.4);
            animation: spin 1.2s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.4));
        }

        .scanner-ring-slow {
            position: absolute;
            inset: 12px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-bottom-color: rgba(212, 175, 55, 0.6);
            border-left-color: rgba(212, 175, 55, 0.3);
            animation: spin 2.5s linear infinite reverse;
        }

        .scanner-dot {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scanner-dot-inner {
            width: 16px;
            height: 16px;
            background: var(--gold-primary);
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.8), 0 0 40px rgba(212, 175, 55, 0.4);
            animation: pulseDot 1.5s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulseDot {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.4);
                opacity: 0.6;
            }
        }

        .loading-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
            letter-spacing: 0.02em;
        }

        .loading-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            letter-spacing: 0.2em;
            text-transform: uppercase;
            font-weight: 500;
        }

        .progress-track {
            width: 100%;
            max-width: 280px;
            height: 4px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 4px;
            margin-top: 2.5rem;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--gold-deep), var(--gold-primary), var(--gold-light));
            border-radius: 4px;
            width: 0;
            animation: progressAnim 2.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.6);
            position: relative;
        }

        @keyframes progressAnim {
            0% {
                width: 0;
            }

            40% {
                width: 45%;
            }

            70% {
                width: 78%;
            }

            100% {
                width: 96%;
            }
        }

        .state-result {
            background: var(--cream);
            color: var(--navy-deep);
        }

        .result-band {
            height: 8px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .band-lulus {
            background: linear-gradient(90deg, #047857, #10b981, #34d399, #10b981, #047857);
            background-size: 200% 100%;
            animation: bandFlow 4s ease-in-out infinite;
        }

        .band-tidak {
            background: linear-gradient(90deg, #be123c, #dc2626, #f87171, #dc2626, #be123c);
            background-size: 200% 100%;
            animation: bandFlow 4s ease-in-out infinite;
        }

        @keyframes bandFlow {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .result-body {
            padding: 3rem 3rem 2.5rem;
            position: relative;
        }

        .result-body::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle at top right, rgba(212, 175, 55, 0.04) 0%, transparent 70%);
            pointer-events: none;
        }

        .result-stamp-area {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .stamp {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: stampIn 0.6s 0.1s cubic-bezier(0.68, -0.55, 0.265, 1.55) both;
            position: relative;
        }

        .stamp::before {
            content: '';
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            background: inherit;
            filter: blur(12px);
            opacity: 0.3;
        }

        .stamp-lulus {
            background: linear-gradient(135deg, rgba(4, 120, 87, 0.08) 0%, rgba(16, 185, 129, 0.12) 100%);
            border: 3px solid rgba(4, 120, 87, 0.3);
            box-shadow: 0 8px 24px rgba(4, 120, 87, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .stamp-tidak {
            background: linear-gradient(135deg, rgba(190, 18, 60, 0.08) 0%, rgba(220, 38, 38, 0.12) 100%);
            border: 3px solid rgba(190, 18, 60, 0.3);
            box-shadow: 0 8px 24px rgba(190, 18, 60, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        @keyframes stampIn {
            0% {
                transform: scale(0.3) rotate(-25deg);
                opacity: 0;
            }

            60% {
                transform: scale(1.1) rotate(5deg);
            }

            100% {
                transform: scale(1) rotate(0);
                opacity: 1;
            }
        }

        .result-label {
            text-align: center;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: rgba(10, 22, 40, 0.4);
            margin-bottom: 0.625rem;
        }

        .result-name {
            text-align: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.6rem, 6vw, 2.2rem);
            font-weight: 700;
            color: var(--navy-deep);
            line-height: 1.2;
            margin-bottom: 0.5rem;
            letter-spacing: 0.01em;
        }

        .result-nisn {
            text-align: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: rgba(10, 22, 40, 0.35);
            margin-bottom: 2rem;
            letter-spacing: 0.15em;
        }

        .cert-box {
            border-radius: 20px;
            padding: 2rem 1.75rem;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .cert-box-lulus {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%);
            border: 2px solid rgba(4, 120, 87, 0.2);
        }

        .cert-box-tidak {
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 50%, #fecdd3 100%);
            border: 2px solid rgba(190, 18, 60, 0.2);
        }

        .cert-desc {
            font-size: 0.9rem;
            line-height: 1.7;
            color: rgba(15, 23, 42, 0.8);
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .cert-verdict {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.2rem, 9vw, 3.2rem);
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .verdict-lulus {
            color: #047857;
        }

        .verdict-tidak {
            color: #be123c;
        }

        .seal-line {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem 1.25rem;
            background: rgba(10, 22, 40, 0.03);
            border-radius: 12px;
            border: 1.5px solid rgba(10, 22, 40, 0.08);
            position: relative;
            overflow: hidden;
        }

        .seal-line::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--gold-primary), var(--gold-deep));
        }

        .seal-icon {
            width: 40px;
            height: 40px;
            background: var(--navy-deep);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .btn-close {
            display: block;
            width: 100%;
            padding: 1.125rem;
            background: var(--navy-deep);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(10, 22, 40, 0.2);
        }

        .btn-close:hover {
            background: var(--navy-light);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(10, 22, 40, 0.3);
        }

        .state-error {
            padding: 3.5rem 3rem;
            text-align: center;
            background: var(--cream);
        }

        .error-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }

        .error-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--navy-deep);
            margin-bottom: 0.75rem;
        }

        .error-msg {
            font-size: 0.875rem;
            color: rgba(10, 22, 40, 0.5);
            line-height: 1.8;
            margin-bottom: 2.5rem;
        }

        .btn-retry {
            width: 100%;
            padding: 1.125rem;
            border: none;
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .shake-error {
            animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-2px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(4px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-6px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(6px, 0, 0);
            }
        }

        @media (max-width: 480px) {

            .result-body,
            .state-error,
            .state-loading {
                padding: 2.5rem 2rem 2rem;
            }
        }
    </style>
</head>

<body x-data="hasilApp">

    <div class="bg-scene">
        <div class="grid-overlay"></div>
        <div class="light-rays"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="particles" id="particles"></div>
    </div>

    <div class="page-wrapper">
        <div class="modal-panel"
            :class="(state === 'error' || studentData.keterangan === 'TIDAK LULUS') ? 'shake-error' : ''" x-cloak>

            <div x-show="state === 'loading'" class="state-loading">
                <div class="scanner">
                    <div class="scanner-ring"></div>
                    <div class="scanner-ring-spin"></div>
                    <div class="scanner-ring-slow"></div>
                    <div class="scanner-dot">
                        <div class="scanner-dot-inner"></div>
                    </div>
                </div>
                <p class="loading-title">Memverifikasi</p>
                <p class="loading-sub">Menghubungkan ke database</p>
                <div class="progress-track">
                    <div class="progress-fill"></div>
                </div>
            </div>

            <div x-show="state === 'result'" class="state-result" style="display: none;">
                <div class="result-band"
                    :class="{ 'band-lulus': studentData.keterangan === 'LULUS', 'band-tidak': studentData.keterangan !== 'LULUS' }">
                </div>
                <div class="result-body">
                    <div class="result-stamp-area">
                        <div class="stamp" :class="studentData.keterangan === 'LULUS' ? 'stamp-lulus' : 'stamp-tidak'">
                            <template x-if="studentData.keterangan === 'LULUS'">
                                <svg style="width:48px;height:48px;color:#047857;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </template>
                            <template x-if="studentData.keterangan !== 'LULUS'">
                                <svg style="width:48px;height:48px;color:#be123c;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </template>
                        </div>
                    </div>

                    <p class="result-label">Atas Nama</p>
                    <h2 class="result-name" x-text="studentData.nama"></h2>
                    <p class="result-nisn">NISN &nbsp;·&nbsp; <span x-text="studentData.nisn"></span></p>

                    <div class="cert-box"
                        :class="{ 'cert-box-lulus': studentData.keterangan === 'LULUS', 'cert-box-tidak': studentData.keterangan !== 'LULUS' }">
                        <p class="cert-desc">Berdasarkan Keputusan Rapat Pleno Dewan Guru SD Negeri Tomang 03, siswa
                            tersebut dinyatakan:</p>
                        <div class="cert-verdict"
                            :class="{'verdict-lulus': studentData.keterangan === 'LULUS', 'verdict-tidak': studentData.keterangan !== 'LULUS'}"
                            x-text="studentData.keterangan"></div>
                    </div>

                    <div class="seal-line">
                        <div class="seal-icon">
                            <svg style="width:20px;height:20px;color:#D4AF37;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="seal-text">
                            <strong>SD Negeri Tomang 03 | Jakarta</strong>
                            <span class="verification-wrapper"
                                style="font-size: 0.75rem; color: #64748b; display: block;">
                                ID Verifikasi: <span style="font-family: monospace; font-weight: 800; color: #4f46e5;"
                                    x-text="studentData.securenumber"></span>
                            </span>
                        </p>
                    </div>

                    <a href="{{ route('kelulusan.pengumuman') }}" class="btn-close">Kembali ke Halaman Awal</a>
                </div>
            </div>

            <div x-show="state === 'ditunda'" class="state-error" style="display: none;">
                <div class="error-icon"
                    style="background-color: #fee2e2; color: #ef4444; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; border: 2px solid #ef4444;">
                    <svg style="width:36px;height:36px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="error-title" style="color: #ef4444;">STATUS DITUNDA</h3>
                <p class="error-msg">Mohon maaf, status kelulusan Anda DITUNDA sementara waktu karena ada catatan
                    khusus. Silakan hubungi Wali Kelas.</p>

                <button class="btn-retry" @click="hubungiWaliKelas()"
                    style="background-color: #10b981; color:#fff; display:flex; align-items:center; justify-content:center; gap:0.5rem; margin-top: 1.5rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                    Hubungi Wali Kelas
                </button>
            </div>

            <div x-show="state === 'loading_prank'" class="state-error" style="padding-top: 2rem; display: none;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem auto; position: relative;">
                    <div style="position: absolute; inset: 0; border: 4px solid #f1f5f9; border-radius: 50%;"></div>
                    <div
                        style="position: absolute; inset: 0; border: 4px solid #6366f1; border-radius: 50%; border-top-color: transparent; animation: spin 1s linear infinite;">
                    </div>
                </div>
                <h3 class="error-title">Menyambungkan...</h3>
                <p class="error-msg">Sedang mengirim log data ke sistem Wali Kelas dan memverifikasi berkas. Mohon
                    tunggu...</p>
                <p
                    style="font-size: 0.75rem; font-weight: bold; color: #94a3b8; text-transform: uppercase; margin-top: 1rem;">
                    Jangan Tutup Halaman Ini</p>
            </div>

        </div>
    </div>

    <script>
        // Inisialisasi Floating Particles Background
        (function initParticles() {
            const container = document.getElementById('particles');
            if(!container) return;
            for (let i = 0; i < 25; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                const size = 1 + Math.random() * 3;
                particle.style.cssText = `
                    left: ${Math.random() * 100}%;
                    width: ${size}px;
                    height: ${size}px;
                    --dur: ${7 + Math.random() * 10}s;
                    --delay: ${-Math.random() * 15}s;
                    --drift: ${(Math.random() - 0.5) * 100}px;
                    animation: particleFloat var(--dur) var(--delay) ease-in-out infinite;
                `;
                container.appendChild(particle);
            }
        })();

        // Mendaftarkan Alpine Component Secara Global via Event Listener
        // Sangat direkomendasikan agar tidak terjadi error "is not defined"
        document.addEventListener('alpine:init', () => {
            Alpine.data('hasilApp', () => ({
                state: 'loading',

                // Gunakan Helper @json bawaan Laravel agar output dari PHP otomatis aman untuk JS
                studentData: {
                    nama: @json(session('studentData')->nama ?? ''),
                    nisn: @json(session('studentData')->nisn ?? ''),
                    keterangan: @json(session('studentData')->keterangan ?? ''),
                    securenumber: @json(session('secureNumber') ?? '')
                },

                init() {
                    // Redirect jika ada user masuk ke link ini langsung padahal tidak ada session
                    if(this.studentData.nisn === '') {
                        window.location.href = "{{ route('kelulusan.pengumuman') }}";
                        return;
                    }

                    // Tahan efek Scanner selama 2.6 Detik
                    setTimeout(() => {
                        if (this.studentData.keterangan === 'DITUNDA') {
                            this.state = 'ditunda';
                        } else {
                            this.state = 'result';
                            if (this.studentData.keterangan === 'LULUS') {
                                this.launchPremiumConfetti();
                            }
                        }
                    }, 2600);
                },

                hubungiWaliKelas() {
                    this.state = 'loading_prank';

                    // Delay prank "menyambungkan" 10 detik, lalu boom -> LULUS
                    setTimeout(() => {
                        this.studentData.keterangan = 'LULUS';
                        this.state = 'result';
                        this.launchPremiumConfetti();
                    }, 10000);
                },

                launchPremiumConfetti() {
                    const colors = ['#D4AF37', '#FFFFFF', '#10b981', '#F4E4C1'];
                    const duration = 5000;
                    const end = Date.now() + duration;

                    const frame = () => {
                        confetti({
                            particleCount: 5, angle: 60, spread: 70, origin: { x: 0, y: 0.6 },
                            colors: colors, startVelocity: 50, gravity: 0.8, scalar: 1.1
                        });
                        confetti({
                            particleCount: 5, angle: 120, spread: 70, origin: { x: 1, y: 0.6 },
                            colors: colors, startVelocity: 50, gravity: 0.8, scalar: 1.1
                        });
                        if (Date.now() < end) requestAnimationFrame(frame);
                    };
                    frame();

                    setTimeout(() => {
                        confetti({
                            particleCount: 80, spread: 90, origin: { x: 0.5, y: 0.5 },
                            colors: colors, scalar: 1.3, gravity: 0.9
                        });
                    }, 250);

                    setTimeout(() => {
                        confetti({
                            particleCount: 50, spread: 60, origin: { x: 0.5, y: 0.6 },
                            colors: colors, scalar: 1.1, gravity: 1
                        });
                    }, 600);
                }
            }));
        });
    </script>
</body>

</html>