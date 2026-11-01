<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Kelulusan: SDN Tomang 03 Pagi</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        :root {
            --gold-primary: #86efac;
            --gold-light: #dcfce7;
            --gold-deep: #166534;
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
        }

        /* ══════════════════════════════════════════════════════════════════
           PREMIUM ANIMATED BACKGROUND
        ══════════════════════════════════════════════════════════════════ */

        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            display: none;
        }

        /* Multi-layer gradient background */
        .bg-scene::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 100% 80% at 20% 0%, rgba(212, 175, 55, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse 90% 70% at 80% 100%, rgba(74, 144, 226, 0.06) 0%, transparent 50%),
                linear-gradient(135deg, #0A1628 0%, #141F38 40%, #0F1A2E 70%, #0A1628 100%);
        }

        /* Premium noise texture overlay */
        .bg-scene::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            opacity: 0.5;
            pointer-events: none;
        }

        /* Floating orbs with enhanced animation */
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

        /* Premium grid pattern */
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

        /* Light rays effect */
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

        /* Enhanced floating particles */
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

        /* ══════════════════════════════════════════════════════════════════
           LAYOUT & STRUCTURE
        ══════════════════════════════════════════════════════════════════ */

        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
            transition: filter 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-blur {
            filter: blur(12px) brightness(0.7);
            transform: scale(0.98);
            opacity: 0.3;
        }

        /* ══════════════════════════════════════════════════════════════════
           SCHOOL BADGE - PREMIUM DESIGN
        ══════════════════════════════════════════════════════════════════ */

        .school-badge {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 3rem;
            opacity: 0;
            animation: fadeSlideUp 1s 0.1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .badge-emblem {
            width: 64px;
            height: 64px;
            background:
                linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(184, 148, 31, 0.15) 100%);
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 8px 24px rgba(212, 175, 55, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .badge-emblem::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        .badge-emblem svg {
            color: var(--gold-primary);
            width: 32px;
            height: 32px;
            filter: drop-shadow(0 2px 8px rgba(212, 175, 55, 0.4));
            position: relative;
            z-index: 1;
        }

        .badge-text {
            text-align: left;
        }

        .badge-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            letter-spacing: 0.02em;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        }

        .badge-sub {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--gold-light);
            letter-spacing: 0.25em;
            text-transform: uppercase;
            margin-top: 4px;
            opacity: 0.9;
        }

        /* Premium ornamental divider */
        .divider-ornament {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            width: 100%;
            max-width: 480px;
            opacity: 0;
            animation: fadeSlideUp 1s 0.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .divider-ornament .line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.5) 20%, rgba(212, 175, 55, 0.8) 50%, rgba(212, 175, 55, 0.5) 80%, transparent);
            position: relative;
        }

        .divider-ornament .diamond {
            width: 8px;
            height: 8px;
            background: var(--gold-primary);
            transform: rotate(45deg);
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.6);
            animation: diamondPulse 2s ease-in-out infinite;
        }

        @keyframes diamondPulse {

            0%,
            100% {
                transform: rotate(45deg) scale(1);
                opacity: 1;
            }

            50% {
                transform: rotate(45deg) scale(1.2);
                opacity: 0.8;
            }
        }

        /* ══════════════════════════════════════════════════════════════════
           HEADLINE - PREMIUM TYPOGRAPHY
        ══════════════════════════════════════════════════════════════════ */

        .headline-block {
            text-align: center;
            margin-bottom: 3rem;
            opacity: 0;
            animation: fadeSlideUp 1s 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .headline-label {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            color: var(--gold-primary);
            margin-bottom: 1rem;
            opacity: 0.9;
            text-shadow: 0 2px 12px rgba(212, 175, 55, 0.3);
        }

        .headline-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.5rem, 10vw, 3.75rem);
            font-weight: 700;
            line-height: 1;
            background: linear-gradient(135deg,
                    #FFFFFF 0%,
                    var(--gold-light) 40%,
                    var(--gold-primary) 70%,
                    var(--gold-deep) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            position: relative;
            display: inline-block;
            animation: titleGlow 3s ease-in-out infinite;
        }

        @keyframes titleGlow {

            0%,
            100% {
                filter: drop-shadow(0 0 20px rgba(212, 175, 55, 0.3));
            }

            50% {
                filter: drop-shadow(0 0 30px rgba(212, 175, 55, 0.5));
            }
        }

        .headline-year {
            display: block;
            font-family: 'Cormorant Garamond', serif;
            font-style: italic;
            font-size: clamp(1rem, 4vw, 1.35rem);
            color: var(--gold-light);
            opacity: 0.85;
            margin-top: 1rem;
            letter-spacing: 0.05em;
        }

        /* ══════════════════════════════════════════════════════════════════
           FORM CARD - GLASSMORPHISM PREMIUM
        ══════════════════════════════════════════════════════════════════ */

        .form-card {
            width: 100%;
            max-width: 480px;
            background: rgba(20, 31, 56, 0.75);
            border: 1.5px solid rgba(212, 175, 55, 0.25);
            border-radius: 28px;
            padding: 3rem 2.5rem;
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.06) inset,
                0 8px 32px rgba(0, 0, 0, 0.3),
                0 24px 64px rgba(0, 0, 0, 0.4),
                0 0 80px rgba(212, 175, 55, 0.08);
            opacity: 0;
            animation: fadeSlideUp 1s 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        /* Premium top accent line */
        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(212, 175, 55, 0.3) 20%,
                    rgba(212, 175, 55, 0.8) 50%,
                    rgba(212, 175, 55, 0.3) 80%,
                    transparent);
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.4);
        }

        /* Subtle corner decoration */
        .form-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle at center, rgba(212, 175, 55, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .card-intro {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .card-intro p {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.8;
            font-weight: 400;
        }

        /* ══════════════════════════════════════════════════════════════════
           FORM FIELDS - PREMIUM INTERACTIONS
        ══════════════════════════════════════════════════════════════════ */

        .field-group {
            margin-bottom: 1.75rem;
            position: relative;
        }

        .field-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--gold-primary);
            margin-bottom: 0.75rem;
            transition: color 0.3s ease;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 1.125rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(212, 175, 55, 0.4);
            width: 18px;
            height: 18px;
            pointer-events: none;
            transition: color 0.3s ease, transform 0.3s ease;
            z-index: 2;
        }

        .field-input {
            width: 100%;
            background: rgba(10, 22, 40, 0.6);
            border: 1.5px solid rgba(212, 175, 55, 0.2);
            border-radius: 14px;
            padding: 1rem 1.125rem 1rem 3rem;
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            color: #fff;
            outline: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }

        .field-input::placeholder {
            color: rgba(255, 255, 255, 0.2);
            transition: color 0.3s ease;
        }

        .field-input:hover {
            border-color: rgba(212, 175, 55, 0.35);
            background: rgba(10, 22, 40, 0.75);
        }

        .field-input:focus {
            border-color: rgba(212, 175, 55, 0.6);
            background: rgba(10, 22, 40, 0.9);
            box-shadow:
                0 0 0 4px rgba(212, 175, 55, 0.1),
                0 8px 24px rgba(212, 175, 55, 0.15);
            transform: translateY(-1px);
        }

        .field-input:focus+.field-icon,
        .field-wrap:has(.field-input:focus) .field-icon {
            color: var(--gold-primary);
            transform: translateY(-50%) scale(1.1);
        }

        .field-input:focus::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        /* Remove number input spinners */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Premium date picker styling */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.7) sepia(1) saturate(3) hue-rotate(5deg);
            opacity: 0.6;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        input[type="date"]:hover::-webkit-calendar-picker-indicator {
            opacity: 1;
        }

        /* ══════════════════════════════════════════════════════════════════
           SUBMIT BUTTON - PREMIUM WITH RIPPLE EFFECT
        ══════════════════════════════════════════════════════════════════ */

        .btn-submit {
            width: 100%;
            padding: 1.125rem 1.75rem;
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-deep) 100%);
            color: var(--navy-deep);
            border: none;
            border-radius: 14px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow:
                0 4px 16px rgba(212, 175, 55, 0.3),
                0 8px 32px rgba(212, 175, 55, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            margin-top: 0.75rem;
        }

        /* Gradient overlay */
        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 60%);
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        /* Shine effect */
        .btn-submit::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
            transform: translateX(-100%) translateY(-100%) rotate(45deg);
            transition: transform 0.6s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow:
                0 8px 24px rgba(212, 175, 55, 0.4),
                0 16px 48px rgba(212, 175, 55, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .btn-submit:hover::after {
            transform: translateX(100%) translateY(100%) rotate(45deg);
        }

        .btn-submit:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }

        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: rippleAnim 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes rippleAnim {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* ══════════════════════════════════════════════════════════════════
           FOOTER
        ══════════════════════════════════════════════════════════════════ */

        .page-footer {
            margin-top: 3rem;
            text-align: center;
            opacity: 0;
            animation: fadeSlideUp 1s 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .page-footer p {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 400;
        }

        .footer-nav-link {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            margin-top: 8px;
            color: var(--gold-primary);
            font-size: 0.875rem;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .footer-nav-link:focus-visible {
            outline: 3px solid var(--gold-primary);
            outline-offset: 3px;
        }

        /* ══════════════════════════════════════════════════════════════════
           MODAL SYSTEM - PREMIUM
        ══════════════════════════════════════════════════════════════════ */

        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 10, 20, 0.94);
            backdrop-filter: blur(16px) saturate(140%);
            -webkit-backdrop-filter: blur(16px) saturate(140%);
        }

        .modal-panel {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            background: var(--cream);
            border-radius: 32px;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.5),
                0 24px 96px rgba(0, 0, 0, 0.6),
                0 48px 128px rgba(0, 0, 0, 0.4);
        }

        .modal-panel::-webkit-scrollbar {
            width: 6px;
        }

        .modal-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-panel::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        .modal-panel::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        /* ══════════════════════════════════════════════════════════════════
           LOADING STATE - PREMIUM
        ══════════════════════════════════════════════════════════════════ */

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
            box-shadow:
                0 0 20px rgba(212, 175, 55, 0.8),
                0 0 40px rgba(212, 175, 55, 0.4);
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
            position: relative;
        }

        .loading-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            letter-spacing: 0.2em;
            text-transform: uppercase;
            font-weight: 500;
        }

        /* Premium progress bar */
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

        .progress-fill::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmerProgress 1.5s ease-in-out infinite;
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

        @keyframes shimmerProgress {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* ══════════════════════════════════════════════════════════════════
           RESULT STATE - PREMIUM
        ══════════════════════════════════════════════════════════════════ */

        .state-result {
            background: var(--cream);
            color: var(--navy-deep);
        }

        /* Top accent band with gradient */
        .result-band {
            height: 8px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .result-band::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: bandShimmer 3s ease-in-out infinite;
        }

        @keyframes bandShimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
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

        .band-ditunda {
            background: linear-gradient(90deg, #92400e, #f59e0b, #fbbf24, #f59e0b, #92400e);
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
            scroll-behavior: smooth;
        }

        .result-body::-webkit-scrollbar {
            width: 6px;
        }

        .result-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .result-body::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }

        /* Decorative corner */
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

        /* Premium stamp with animation */
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
            background:
                linear-gradient(135deg, rgba(4, 120, 87, 0.08) 0%, rgba(16, 185, 129, 0.12) 100%);
            border: 3px solid rgba(4, 120, 87, 0.3);
            box-shadow:
                0 8px 24px rgba(4, 120, 87, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .stamp-tidak {
            background:
                linear-gradient(135deg, rgba(190, 18, 60, 0.08) 0%, rgba(220, 38, 38, 0.12) 100%);
            border: 3px solid rgba(190, 18, 60, 0.3);
            box-shadow:
                0 8px 24px rgba(190, 18, 60, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
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

        /* Premium certificate box */
        .cert-box {
            border-radius: 20px;
            padding: 2rem 1.75rem;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .cert-box::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.05;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .cert-box-lulus {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%);
            border: 2px solid rgba(4, 120, 87, 0.2);
        }

        .cert-box-tidak {
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 50%, #fecdd3 100%);
            border: 2px solid rgba(190, 18, 60, 0.2);
        }

        .cert-box-ditunda {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #fde68a 100%);
            border: 2px solid rgba(146, 64, 14, 0.2);
        }

        .cert-desc {
            font-size: 0.9rem;
            /* Ukuran diperbesar (setara ~16.8px) */
            line-height: 1.7;
            /* Jarak antar baris yang ideal untuk teks resmi */
            color: rgba(15, 23, 42, 0.8);
            /* Warna dibuat lebih pekat dan tegas (Slate 800) */
            margin-bottom: 2rem;
            /* Memberi jarak aman ke tulisan status di bawahnya */
            font-weight: 600;
            /* Diubah ke Semi-Bold agar karakter Jakarta Sans lebih kuat */
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

        .verdict-ditunda {
            color: #92400e;
        }

        .cert-verdict-sub {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            margin-top: 0.75rem;
            opacity: 0.6;
        }

        /* Decorative corner sparkle */
        .cert-box::after {
            content: '✦';
            position: absolute;
            bottom: 1rem;
            right: 1.25rem;
            font-size: 0.7rem;
            opacity: 0.15;
            animation: sparkle 3s ease-in-out infinite;
        }

        @keyframes sparkle {

            0%,
            100% {
                opacity: 0.15;
                transform: rotate(0deg) scale(1);
            }

            50% {
                opacity: 0.3;
                transform: rotate(180deg) scale(1.2);
            }
        }

        /* Premium school seal */
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
            box-shadow: 0 4px 12px rgba(10, 22, 40, 0.15);
        }

        .seal-icon svg {
            width: 20px;
            height: 20px;
            color: var(--gold-primary);
        }

        .seal-text {
            font-size: 0.75rem;
            color: rgba(10, 22, 40, 0.55);
            line-height: 1.6;
        }

        .seal-text strong {
            color: var(--navy-deep);
            font-weight: 600;
            display: block;
            font-size: 0.8rem;
            margin-bottom: 2px;
        }

        /* Premium close button */
        .btn-close {
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
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(10, 22, 40, 0.2);
        }

        .btn-close:hover {
            background: var(--navy-light);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(10, 22, 40, 0.3);
        }

        .btn-close:active {
            transform: translateY(0) scale(0.98);
        }

        /* ══════════════════════════════════════════════════════════════════
           ERROR STATE
        ══════════════════════════════════════════════════════════════════ */

        .state-error {
            padding: 3.5rem 3rem;
            text-align: center;
            background: var(--cream);
        }

        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #fff1f2, #ffe4e6);
            border: 2.5px solid rgba(190, 18, 60, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 8px 24px rgba(190, 18, 60, 0.15);
            animation: errorPulse 2s ease-in-out infinite;
        }

        @keyframes errorPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .error-icon svg {
            width: 36px;
            height: 36px;
            color: #be123c;
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
            background: #be123c;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(190, 18, 60, 0.3);
        }

        .btn-retry:hover {
            background: #9f1239;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(190, 18, 60, 0.4);
        }

        .btn-retry:active {
            transform: translateY(0) scale(0.98);
        }

        /* ══════════════════════════════════════════════════════════════════
           ANTI-INSPECT PROTECTION STYLING
        ══════════════════════════════════════════════════════════════════ */

        .devtools-warning {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            font-family: 'Montserrat', sans-serif;
            color: #fff;
            text-align: center;
            padding: 2rem;
            animation: fadeIn 0.3s ease;
        }

        .devtools-content {
            max-width: 400px;
        }

        .devtools-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }

        .devtools-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #DC2626;
        }

        .devtools-message {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .devtools-code {
            font-family: 'JetBrains Mono', monospace;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            font-size: 0.85rem;
            color: #166534;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* ══════════════════════════════════════════════════════════════════
           ANIMATIONS
        ══════════════════════════════════════════════════════════════════ */

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Shake animation for errors */
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

        /* ══════════════════════════════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════════════════════════════ */

        @media (max-width: 480px) {
            .form-card {
                padding: 2.5rem 2rem;
            }

            .result-body {
                padding: 2.5rem 2rem 2rem;
            }

            .headline-block {
                margin-bottom: 2.5rem;
            }

            .badge-emblem {
                width: 56px;
                height: 56px;
            }

            .badge-emblem svg {
                width: 28px;
                height: 28px;
            }

            .scanner {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <header>
        <div class="school-badge">
            <div class="badge-emblem">
                <svg aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <div class="badge-text">
                <div class="badge-name">SDN Tomang 03 Pagi</div>
                <div class="badge-sub">Jakarta Barat</div>
            </div>
        </div>

        <div class="divider-ornament">
            <div class="line"></div>
            <div class="diamond"></div>
            <div class="line"></div>
        </div>

        <div class="headline-block">
            <p class="headline-label">Pengumuman Resmi</p>
            <h1 class="headline-title">Portal Kelulusan</h1>
            <span class="headline-year">Tahun Ajaran 2025 / 2026</span>
        </div>
        </header>

        <main class="form-card">
            <div class="card-intro">
                <p>Masukkan NISN dan tanggal lahir siswa untuk mengetahui status kelulusan secara resmi dan
                    terverifikasi.</p>
            </div>

            @if(session('error'))
            <div
                style="background: rgba(220, 38, 38, 0.15); border: 1px solid rgba(220, 38, 38, 0.4); color: #fca5a5; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center; font-size: 0.85rem; font-weight: 500;">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('kelulusan.cek') }}" method="POST">
                @csrf
                <div class="field-group">
                    <label class="field-label" for="nisn">NISN Siswa</label>
                    <div class="field-wrap">
                        <svg class="field-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                        </svg>
                        <input type="number" id="nisn" name="nisn" class="field-input" placeholder="Masukkan NISN" required
                            maxlength="10">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label" for="tanggal_lahir">Tanggal Lahir</label>
                    <div class="field-wrap">
                        <svg class="field-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="field-input" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <div class="btn-inner">Verifikasi Status Kelulusan</div>
                </button>
            </form>
        </main>

        <footer class="page-footer">
            <p>Sistem Informasi Akademik &copy; 2026 · SDN Tomang 03</p>
            <nav aria-label="Navigasi utama">
                <a class="footer-nav-link" href="{{ url('/') }}">Halaman utama</a>
            </nav>
        </footer>
    </div>
    <script>
        // ══════════════════════════════════════════════════════════════════
        // ALPINE.JS APPLICATION
        // ══════════════════════════════════════════════════════════════════
        function kelulusanApp() {
            return {
                showModal: false,
                modalState: 'hidden', // 'hidden' | 'loading' | 'result' | 'error'
                errorMessage: '',
                formData: {
                    nisn: '',
                    tanggal_lahir: ''
                },
                studentData: {
                    nama: '',
                    nisn: '',
                    keterangan: ''
                },

                // Ripple effect on button click
                createRipple(event) {
                    const button = event.currentTarget;
                    const ripple = document.createElement('span');
                    const rect = button.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = event.clientX - rect.left - size / 2;
                    const y = event.clientY - rect.top - size / 2;

                    ripple.className = 'ripple';
                    ripple.style.cssText = `
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                    `;

                    button.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                },

                // Check graduation status
                async cekData() {
        this.showModal = true;
        this.modalState = 'loading';

        const metaToken = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = metaToken ? metaToken.getAttribute('content') : '';

        try {
            const response = await fetch('/api/kelulusan/cek', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(this.formData)
            });

            const result = await response.json();

            // Simulasi loading pencarian awal
            setTimeout(() => {
                if (response.ok && result.status === 'success') {
                    this.studentData = result.data;

                    // ==========================================
                    // LOGIKA PRANK BERDASARKAN DATABASE
                    // ==========================================

                    if (this.studentData.keterangan === 'DITUNDA') {
                        // 1. KHUSUS YANG STATUSNYA "DITUNDA": Masuk ke mode prank
                        this.modalState = 'ditunda';
                        this.errorMessage = 'Mohon maaf, status kelulusan Anda DITUNDA sementara waktu karena ada catatan khusus. Silakan hubungi Wali Kelas Anda untuk konfirmasi lebih lanjut.';

                    } else if (this.studentData.keterangan === 'LULUS') {
                        // 2. YANG STATUSNYA "LULUS": Langsung tampilkan hasil tanpa prank
                        this.modalState = 'result';
                        this.launchPremiumConfetti();

                    } else {
                        // 3. STATUS LAINNYA (TIDAK LULUS): Langsung tampilkan hasil
                         this.errorMessage = result.message || 'NISN ditemukan dalam database, namun tanggal lahir tidak cocok. Pastikan data yang dimasukkan sudah benar. Hubungi wali kelas untuk bantuan lebih lanjut!';
                    this.modalState = 'error';
                        this.modalState = 'error';
                    }

                } else {
                    // Data tidak ditemukan / NISN Salah
                    this.errorMessage = result.message || 'NISN atau tanggal lahir tidak terdaftar dalam sistem kami. Pastikan data yang dimasukkan sudah benar.';
                    this.modalState = 'error';
                }
            }, 2600);

        } catch (error) {
            setTimeout(() => {
                this.errorMessage = 'Gagal terhubung ke server. Periksa koneksi internet Anda dan coba lagi.';
                this.modalState = 'error';
            }, 1200);
        }
    },

    // ==========================================
    // FUNGSI PICUAN TOMBOL "HUBUNGI WALI KELAS"
    // ==========================================
    hubungiWaliKelas() {
        // 1. Ubah layar ke mode menyambungkan
        this.modalState = 'loading_prank';

        // 2. Tahan selama 10 detik penuh
        setTimeout(() => {

            // 3. Setelah 10 detik, ubah status anak tersebut menjadi "LULUS" secara paksa di layar mereka
            this.studentData.keterangan = 'LULUS';

            // 4. Pindahkan ke layar hasil
            this.modalState = 'result';

            // 5. BOOM! Ledakkan Confetti
            this.launchPremiumConfetti();

        }, 10000);
    },
                // Premium confetti effect
                launchPremiumConfetti() {
                    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

                    const colors = ['#255B4B', '#FFFFFF', '#8A5E13', '#DCEBE3'];
                    const duration = 5000;
                    const end = Date.now() + duration;

                    // Side cannons
                    const frame = () => {
                        confetti({
                            particleCount: 5,
                            angle: 60,
                            spread: 70,
                            origin: { x: 0, y: 0.6 },
                            colors: colors,
                            startVelocity: 50,
                            gravity: 0.8,
                            scalar: 1.1
                        });
                        confetti({
                            particleCount: 5,
                            angle: 120,
                            spread: 70,
                            origin: { x: 1, y: 0.6 },
                            colors: colors,
                            startVelocity: 50,
                            gravity: 0.8,
                            scalar: 1.1
                        });

                        if (Date.now() < end) {
                            requestAnimationFrame(frame);
                        }
                    };
                    frame();

                    // Center burst
                    setTimeout(() => {
                        confetti({
                            particleCount: 80,
                            spread: 90,
                            origin: { x: 0.5, y: 0.5 },
                            colors: colors,
                            scalar: 1.3,
                            gravity: 0.9
                        });
                    }, 250);

                    // Secondary burst
                    setTimeout(() => {
                        confetti({
                            particleCount: 50,
                            spread: 60,
                            origin: { x: 0.5, y: 0.6 },
                            colors: colors,
                            scalar: 1.1,
                            gravity: 1
                        });
                    }, 600);
                },

                // Reset modal state
                resetModal() {
                    this.showModal = false;

                    setTimeout(() => {
                        this.modalState = 'hidden';
                        this.errorMessage = '';

                        // Clear form only if student passed
                        if (this.studentData.keterangan === 'LULUS') {
                            this.formData.nisn = '';
                            this.formData.tanggal_lahir = '';
                        }

                        this.studentData = {
                            nama: '',
                            nisn: '',
                            keterangan: ''
                        };
                    }, 400);
                }
            };
        }
    </script>
</body>

</html>