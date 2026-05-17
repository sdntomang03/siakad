<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Kelulusan — SDN Tomang 03 Pagi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap"
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
            --gold: #C9A84C;
            --gold-light: #E8C97A;
            --gold-pale: #F5E6C0;
            --navy: #0B1426;
            --navy-mid: #132040;
            --navy-card: #0F1C36;
            --cream: #FAF7F0;
            --text-muted: rgba(255, 255, 255, 0.45);
        }

        [x-cloak] {
            display: none !important;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--navy);
            color: #fff;
            overflow-x: hidden;
            min-height: 100dvh;
        }

        /* ── ANIMATED BG ── */
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
                radial-gradient(ellipse 80% 60% at 10% 0%, rgba(201, 168, 76, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 90% 100%, rgba(19, 32, 64, 0.8) 0%, transparent 60%),
                linear-gradient(160deg, #0B1426 0%, #0F1C36 50%, #091220 100%);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: drift 18s ease-in-out infinite;
            pointer-events: none;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(201, 168, 76, 0.08) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(30, 60, 120, 0.2) 0%, transparent 70%);
            bottom: -50px;
            right: -80px;
            animation-delay: -9s;
        }

        @keyframes drift {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -20px) scale(1.05);
            }

            66% {
                transform: translate(-20px, 15px) scale(0.95);
            }
        }

        /* GRID TEXTURE */
        .grid-texture {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(201, 168, 76, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201, 168, 76, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
        }

        /* FLOATING PARTICLES */
        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: var(--gold);
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat var(--dur, 8s) var(--delay, 0s) ease-in-out infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-10vh) translateX(var(--drift, 20px));
                opacity: 0;
            }
        }

        /* ── LAYOUT ── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        /* ── HEADER ── */
        .school-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2.5rem;
            gap: 1rem;
            opacity: 0;
            animation: fadeUp 0.8s 0.1s ease forwards;
        }

        .badge-emblem {
            width: 52px;
            height: 52px;
            border: 1.5px solid rgba(201, 168, 76, 0.4);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(201, 168, 76, 0.08);
            flex-shrink: 0;
        }

        .badge-emblem svg {
            color: var(--gold);
            width: 26px;
            height: 26px;
        }

        .badge-text {
            text-align: left;
        }

        .badge-name {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            letter-spacing: 0.01em;
        }

        .badge-sub {
            font-size: 0.65rem;
            font-weight: 500;
            color: var(--gold);
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .divider-ornament {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeUp 0.8s 0.2s ease forwards;
        }

        .divider-ornament .line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201, 168, 76, 0.4), transparent);
        }

        .divider-ornament .diamond {
            width: 6px;
            height: 6px;
            background: var(--gold);
            transform: rotate(45deg);
            flex-shrink: 0;
        }

        /* ── HEADLINE ── */
        .headline-block {
            text-align: center;
            margin-bottom: 2.5rem;
            opacity: 0;
            animation: fadeUp 0.8s 0.3s ease forwards;
        }

        .headline-label {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.75rem;
        }

        .headline-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 8vw, 3rem);
            font-weight: 900;
            line-height: 1.1;
            background: linear-gradient(135deg, #FFFFFF 30%, var(--gold-light) 70%, var(--gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
        }

        .headline-year {
            display: inline-block;
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: clamp(1rem, 4vw, 1.25rem);
            color: var(--gold-light);
            opacity: 0.8;
            margin-top: 0.5rem;
        }

        /* ── CARD ── */
        .form-card {
            width: 100%;
            max-width: 440px;
            background: rgba(15, 28, 54, 0.8);
            border: 1px solid rgba(201, 168, 76, 0.2);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.04) inset,
                0 40px 80px rgba(0, 0, 0, 0.5),
                0 0 60px rgba(201, 168, 76, 0.04);
            opacity: 0;
            animation: fadeUp 0.8s 0.5s ease forwards;
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201, 168, 76, 0.5), transparent);
        }

        .card-intro {
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-intro p {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.7;
            font-weight: 400;
        }

        /* FIELDS */
        .field-group {
            margin-bottom: 1.5rem;
        }

        .field-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.625rem;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(201, 168, 76, 0.5);
            width: 16px;
            height: 16px;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            background: rgba(11, 20, 38, 0.6);
            border: 1px solid rgba(201, 168, 76, 0.2);
            border-radius: 12px;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: #fff;
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            -webkit-appearance: none;
            appearance: none;
        }

        .field-input::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .field-input:focus {
            border-color: rgba(201, 168, 76, 0.5);
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.08);
            background: rgba(11, 20, 38, 0.9);
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.6) sepia(1) saturate(2) hue-rotate(5deg);
            opacity: 0.5;
            cursor: pointer;
        }

        /* SUBMIT BUTTON */
        .btn-submit {
            width: 100%;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, var(--gold) 0%, #A8872F 100%);
            color: var(--navy);
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            box-shadow: 0 8px 24px rgba(201, 168, 76, 0.25), 0 2px 8px rgba(0, 0, 0, 0.3);
            margin-top: 0.5rem;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 32px rgba(201, 168, 76, 0.35), 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0) scale(0.99);
            opacity: 0.9;
        }

        .btn-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            position: relative;
            z-index: 1;
        }

        /* FOOTER */
        .page-footer {
            margin-top: 2.5rem;
            text-align: center;
            opacity: 0;
            animation: fadeUp 0.8s 0.7s ease forwards;
        }

        .page-footer p {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.2);
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        /* ── MODAL ── */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            overflow-y: auto;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 10, 20, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .modal-panel {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            background: var(--cream);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 60px 120px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.5);
        }

        /* LOADING STATE */
        .state-loading {
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: #0B1426;
            color: #fff;
        }

        .scanner {
            width: 100px;
            height: 100px;
            position: relative;
            margin-bottom: 2rem;
        }

        .scanner-ring {
            position: absolute;
            inset: 0;
            border: 2px solid rgba(201, 168, 76, 0.2);
            border-radius: 50%;
        }

        .scanner-ring-spin {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top-color: var(--gold);
            border-right-color: rgba(201, 168, 76, 0.3);
            animation: spin 1s linear infinite;
        }

        .scanner-ring-slow {
            position: absolute;
            inset: 10px;
            border-radius: 50%;
            border: 1px solid transparent;
            border-bottom-color: rgba(201, 168, 76, 0.5);
            animation: spin 2s linear infinite reverse;
        }

        .scanner-dot {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scanner-dot-inner {
            width: 12px;
            height: 12px;
            background: var(--gold);
            border-radius: 50%;
            animation: pulse-dot 1.5s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse-dot {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.5);
                opacity: 0.5;
            }
        }

        .loading-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .loading-sub {
            font-size: 0.7rem;
            color: var(--text-muted);
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        /* PROGRESS BAR */
        .progress-track {
            width: 100%;
            height: 3px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 2px;
            margin-top: 2rem;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            border-radius: 2px;
            width: 0;
            animation: progressAnim 2.5s ease forwards;
        }

        @keyframes progressAnim {
            0% {
                width: 0
            }

            40% {
                width: 40%
            }

            70% {
                width: 75%
            }

            100% {
                width: 95%
            }
        }

        /* RESULT STATE */
        .state-result {
            background: var(--cream);
            color: var(--navy);
        }

        /* RESULT HEADER BAND */
        .result-band {
            height: 6px;
            width: 100%;
        }

        .band-lulus {
            background: linear-gradient(90deg, #047857, #10b981, #047857);
        }

        .band-tidak {
            background: linear-gradient(90deg, #be123c, #f43f5e, #be123c);
        }

        .band-ditunda {
            background: linear-gradient(90deg, #92400e, #f59e0b, #92400e);
        }

        .result-body {
            padding: 2.5rem 2.5rem 2rem;
        }

        /* STAMP */
        .result-stamp-area {
            display: flex;
            justify-content: center;
            margin-bottom: 1.75rem;
        }

        .stamp {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: stampIn 0.5s 0.1s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        .stamp-lulus {
            background: rgba(4, 120, 87, 0.1);
            border: 2px solid rgba(4, 120, 87, 0.25);
        }

        .stamp-tidak {
            background: rgba(190, 18, 60, 0.1);
            border: 2px solid rgba(190, 18, 60, 0.25);
        }

        @keyframes stampIn {
            from {
                transform: scale(0.5) rotate(-15deg);
                opacity: 0;
            }

            to {
                transform: scale(1) rotate(0);
                opacity: 1;
            }
        }

        .result-label {
            text-align: center;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: rgba(11, 20, 38, 0.45);
            margin-bottom: 0.5rem;
        }

        .result-name {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.4rem, 5vw, 1.9rem);
            font-weight: 700;
            color: var(--navy);
            line-height: 1.2;
            margin-bottom: 0.375rem;
            letter-spacing: -0.01em;
        }

        .result-nisn {
            text-align: center;
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            color: rgba(11, 20, 38, 0.4);
            margin-bottom: 1.75rem;
            letter-spacing: 0.1em;
        }

        /* CERTIFICATE BOX */
        .cert-box {
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.75rem;
            position: relative;
            overflow: hidden;
        }

        .cert-box-lulus {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 1.5px solid rgba(4, 120, 87, 0.2);
        }

        .cert-box-tidak {
            background: linear-gradient(135deg, #fff1f2, #ffe4e6);
            border: 1.5px solid rgba(190, 18, 60, 0.2);
        }

        .cert-box-ditunda {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1.5px solid rgba(146, 64, 14, 0.2);
        }

        .cert-desc {
            font-size: 0.75rem;
            line-height: 1.7;
            color: rgba(11, 20, 38, 0.55);
            margin-bottom: 1.25rem;
            font-weight: 400;
        }

        .cert-verdict {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 8vw, 2.75rem);
            font-weight: 900;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .verdict-lulus {
            color: #047857;
        }

        .verdict-tidak {
            color: #be123c;
        }

        .verdict-ditunda {
            color: #92400e;
        }

        .cert-verdict-sub {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-top: 0.5rem;
            opacity: 0.6;
        }

        /* ORNAMENTAL CORNER */
        .cert-box::after {
            content: '✦';
            position: absolute;
            bottom: 0.5rem;
            right: 0.75rem;
            font-size: 0.6rem;
            opacity: 0.2;
        }

        /* SCHOOL SEAL LINE */
        .seal-line {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
            padding: 0.875rem 1rem;
            background: rgba(11, 20, 38, 0.04);
            border-radius: 10px;
            border: 1px solid rgba(11, 20, 38, 0.08);
        }

        .seal-icon {
            width: 32px;
            height: 32px;
            background: var(--navy);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .seal-icon svg {
            width: 16px;
            height: 16px;
            color: var(--gold);
        }

        .seal-text {
            font-size: 0.72rem;
            color: rgba(11, 20, 38, 0.6);
            line-height: 1.5;
        }

        .seal-text strong {
            color: var(--navy);
            font-weight: 600;
            display: block;
            font-size: 0.75rem;
        }

        /* CLOSE BUTTON */
        .btn-close {
            width: 100%;
            padding: 1rem;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-close:hover {
            background: var(--navy-mid);
        }

        .btn-close:active {
            transform: scale(0.99);
        }

        /* ERROR STATE */
        .state-error {
            padding: 3rem 2.5rem;
            text-align: center;
            background: var(--cream);
        }

        .error-icon {
            width: 72px;
            height: 72px;
            background: #fff1f2;
            border: 2px solid rgba(190, 18, 60, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .error-icon svg {
            width: 32px;
            height: 32px;
            color: #be123c;
        }

        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 0.625rem;
        }

        .error-msg {
            font-size: 0.82rem;
            color: rgba(11, 20, 38, 0.55);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .btn-retry {
            width: 100%;
            padding: 1rem;
            background: #be123c;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-retry:hover {
            background: #9f1239;
        }

        .btn-retry:active {
            transform: scale(0.99);
        }

        /* TRANSITIONS */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .modal-enter {
            animation: modalIn 0.4s cubic-bezier(0.34, 1.3, 0.64, 1) forwards;
        }

        .modal-leave {
            animation: modalOut 0.3s ease forwards;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.92) translateY(12px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes modalOut {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(0.95);
            }
        }

        .shake-error {
            animation: shakeErr 0.45s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shakeErr {

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

        /* BLUR MAIN ON MODAL */
        .main-blur {
            filter: blur(8px);
            transform: scale(1.02);
            opacity: 0.4;
            transition: all 0.5s ease;
        }

        .main-normal {
            filter: blur(0);
            transform: scale(1);
            opacity: 1;
            transition: all 0.5s ease;
        }
    </style>
</head>

<body x-data="kelulusanApp()">

    <!-- ── BACKGROUND ── -->
    <div class="bg-scene">
        <div class="grid-texture"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="particles" id="particles"></div>
    </div>

    <!-- ── MAIN PAGE ── -->
    <div class="page-wrapper" :class="showModal ? 'main-blur' : 'main-normal'">

        <!-- School Badge -->
        <div class="school-badge">
            <div class="badge-emblem">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <div class="badge-text">
                <div class="badge-name">SDN Tomang 03 Pagi</div>
                <div class="badge-sub">Jakarta Barat</div>
            </div>
        </div>

        <!-- Ornament Divider -->
        <div class="divider-ornament" style="width:100%;max-width:440px;">
            <div class="line"></div>
            <div class="diamond"></div>
            <div class="line"></div>
        </div>

        <!-- Headline -->
        <div class="headline-block">
            <p class="headline-label">Pengumuman Resmi</p>
            <h1 class="headline-title">Portal<br>Kelulusan</h1>
            <span class="headline-year">Tahun Ajaran 2025 / 2026</span>
        </div>

        <!-- Form Card -->
        <div class="form-card" style="width:100%;max-width:440px;">
            <div class="card-intro">
                <p>Masukkan NISN dan tanggal lahir siswa untuk mengetahui status kelulusan secara resmi dan
                    terverifikasi.</p>
            </div>

            <form @submit.prevent="cekData">
                <div class="field-group">
                    <label class="field-label">NISN Siswa</label>
                    <div class="field-wrap">
                        <svg class="field-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                        </svg>
                        <input type="number" class="field-input" placeholder="10 digit NISN" inputmode="numeric"
                            x-model="formData.nisn" required maxlength="10">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Tanggal Lahir</label>
                    <div class="field-wrap">
                        <svg class="field-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <input type="date" class="field-input" x-model="formData.tanggal_lahir" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <div class="btn-inner">
                        <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Verifikasi Status Kelulusan
                    </div>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="page-footer">
            <p>Sistem Informasi Akademik &copy; 2026 · SDN Tomang 03 Pagi</p>
        </div>

    </div>

    <!-- ── MODAL ── -->
    <div class="modal-backdrop" x-show="showModal" x-cloak style="display:none;">

        <!-- Overlay -->
        <div class="modal-overlay" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-250"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- Panel -->
        <div class="modal-panel"
            :class="(modalState === 'error' || studentData.keterangan === 'TIDAK LULUS') ? 'shake-error' : ''"
            x-show="showModal" x-transition:enter="ease-out duration-400"
            x-transition:enter-start="opacity-0 scale-90 translateY-4"
            x-transition:enter-end="opacity-100 scale-100 translateY-0" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <!-- LOADING -->
            <div x-show="modalState === 'loading'" class="state-loading">
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

            <!-- RESULT -->
            <div x-show="modalState === 'result'" style="display:none;" class="state-result">

                <!-- Top color band -->
                <div class="result-band" :class="{
          'band-lulus': studentData.keterangan === 'LULUS',
          'band-tidak': studentData.keterangan === 'TIDAK LULUS',
          'band-ditunda': studentData.keterangan === 'DITUNDA'
        }">
                </div>

                <div class="result-body">
                    <!-- Stamp icon -->
                    <div class="result-stamp-area">
                        <div class="stamp" :class="studentData.keterangan === 'LULUS' ? 'stamp-lulus' : 'stamp-tidak'">
                            <template x-if="studentData.keterangan === 'LULUS'">
                                <svg style="width:44px;height:44px;color:#047857;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </template>
                            <template x-if="studentData.keterangan !== 'LULUS'">
                                <svg style="width:44px;height:44px;color:#be123c;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </template>
                        </div>
                    </div>

                    <!-- Student info -->
                    <p class="result-label">Atas Nama</p>
                    <h2 class="result-name" x-text="studentData.nama"></h2>
                    <p class="result-nisn">NISN &nbsp;·&nbsp; <span x-text="studentData.nisn"></span></p>

                    <!-- Certificate box -->
                    <div class="cert-box" :class="{
            'cert-box-lulus': studentData.keterangan === 'LULUS',
            'cert-box-tidak': studentData.keterangan === 'TIDAK LULUS',
            'cert-box-ditunda': studentData.keterangan === 'DITUNDA'
          }">
                        <p class="cert-desc">
                            Berdasarkan Keputusan Rapat Pleno Dewan Guru SDN Tomang 03 Pagi Tahun Ajaran 2025/2026,
                            siswa tersebut dinyatakan:
                        </p>
                        <div class="cert-verdict" :class="{
              'verdict-lulus': studentData.keterangan === 'LULUS',
              'verdict-tidak': studentData.keterangan === 'TIDAK LULUS',
              'verdict-ditunda': studentData.keterangan === 'DITUNDA'
            }" x-text="studentData.keterangan">
                        </div>
                        <p class="cert-verdict-sub" :class="{
              'verdict-lulus': studentData.keterangan === 'LULUS',
              'verdict-tidak': studentData.keterangan === 'TIDAK LULUS',
              'verdict-ditunda': studentData.keterangan === 'DITUNDA'
            }">
                            <span x-show="studentData.keterangan === 'LULUS'">dari Satuan Pendidikan</span>
                            <span x-show="studentData.keterangan !== 'LULUS'">Hubungi pihak sekolah</span>
                        </p>
                    </div>

                    <!-- School seal -->
                    <div class="seal-line">
                        <div class="seal-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="seal-text">
                            <strong>SDN Tomang 03 Pagi — Jakarta Barat</strong>
                            Keputusan bersifat resmi dan terverifikasi sistem
                        </p>
                    </div>

                    <!-- Close button -->
                    <button class="btn-close" @click="resetModal">Tutup &amp; Kembali</button>
                </div>
            </div>

            <!-- ERROR -->
            <div x-show="modalState === 'error'" style="display:none;" class="state-error">
                <div class="error-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 class="error-title">Data Tidak Ditemukan</h3>
                <p class="error-msg" x-text="errorMessage"></p>
                <button class="btn-retry" @click="resetModal">Ulangi Pengecekan</button>
            </div>

        </div>
    </div>

    <script>
        // ── PARTICLES ──
  (function() {
    const container = document.getElementById('particles');
    for (let i = 0; i < 20; i++) {
      const p = document.createElement('div');
      p.className = 'particle';
      p.style.cssText = `
        left: ${Math.random() * 100}%;
        --dur: ${6 + Math.random() * 8}s;
        --delay: ${-Math.random() * 12}s;
        --drift: ${(Math.random() - 0.5) * 80}px;
        width: ${1 + Math.random() * 2}px;
        height: ${1 + Math.random() * 2}px;
        opacity: 0;
      `;
      container.appendChild(p);
    }
  })();

  // ── ALPINE APP ──
  function kelulusanApp() {
    return {
      showModal: false,
      modalState: 'hidden',
      errorMessage: '',
      formData: { nisn: '', tanggal_lahir: '' },
      studentData: { nama: '', nisn: '', keterangan: '' },

      async cekData() {
        this.showModal = true;
        this.modalState = 'loading';

        try {
          const response = await fetch('/api/kelulusan/cek', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(this.formData)
          });

          const result = await response.json();

          setTimeout(() => {
            if (response.ok && result.status === 'success') {
              this.studentData = result.data;
              this.modalState = 'result';
              if (this.studentData.keterangan === 'LULUS') this.launchConfetti();
            } else {
              this.errorMessage = result.message || 'NISN atau tanggal lahir tidak terdaftar dalam sistem kami. Pastikan data yang dimasukkan sudah benar.';
              this.modalState = 'error';
            }
          }, 2600);

        } catch (e) {
          setTimeout(() => {
            this.errorMessage = 'Gagal terhubung ke server. Periksa koneksi internet Anda dan coba lagi.';
            this.modalState = 'error';
          }, 1200);
        }
      },

      launchConfetti() {
        const gold = '#C9A84C', white = '#FFFFFF', green = '#10b981';
        const end = Date.now() + 4000;
        const colors = [gold, white, green];

        const shoot = () => {
          confetti({ particleCount: 4, angle: 60, spread: 60, origin: { x: 0 }, colors, startVelocity: 45 });
          confetti({ particleCount: 4, angle: 120, spread: 60, origin: { x: 1 }, colors, startVelocity: 45 });
          if (Date.now() < end) requestAnimationFrame(shoot);
        };
        shoot();

        // Center burst
        setTimeout(() => {
          confetti({ particleCount: 60, spread: 70, origin: { x: 0.5, y: 0.6 }, colors, scalar: 1.2 });
        }, 200);
      },

      resetModal() {
        this.showModal = false;
        setTimeout(() => {
          this.modalState = 'hidden';
          this.errorMessage = '';
          if (this.studentData.keterangan === 'LULUS') {
            this.formData.nisn = '';
            this.formData.tanggal_lahir = '';
          }
          this.studentData = { nama: '', nisn: '', keterangan: '' };
        }, 400);
      }
    };
  }
    </script>
</body>

</html>