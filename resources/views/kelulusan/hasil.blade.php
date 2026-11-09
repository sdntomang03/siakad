@php
    $student = session('studentData');
    $birthDate = \Illuminate\Support\Carbon::parse($student->tanggal_lahir);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>Hasil Kelulusan | SDN Tomang 03 Pagi</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            --page: #f5f6f2;
            --surface: #ffffff;
            --surface-muted: #f1f7f3;
            --text: #1f2937;
            --muted: #526055;
            --border: #d7dfd8;
            --brand: #255b4b;
            --brand-strong: #173c32;
            --accent: #8a5e13;
            --success-bg: #e8f4ec;
            --success-text: #1d4b3e;
            --warning-bg: #fff7df;
            --warning-text: #713f12;
            --danger-bg: #fdf0ef;
            --danger-text: #8f2823;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            min-width: 0;
            min-height: 100vh;
            margin: 0;
            background: var(--page);
            color: var(--text);
            line-height: 1.6;
        }

        a {
            color: inherit;
            cursor: pointer;
        }

        a:focus-visible {
            outline: 3px solid var(--accent);
            outline-offset: 3px;
        }

        .skip-link {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            z-index: 1;
            padding: 0.65rem 0.9rem;
            transform: translateY(-160%);
            border-radius: 0.375rem;
            background: var(--surface);
            color: var(--brand-strong);
            font-weight: 600;
        }

        .skip-link:focus {
            transform: translateY(0);
        }

        .page-shell {
            width: min(100% - 2rem, 56rem);
            margin-inline: auto;
            padding-block: clamp(1.5rem, 5vw, 3.5rem);
        }

        .school-header {
            margin-bottom: clamp(1.5rem, 4vw, 2.5rem);
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .school-name {
            margin: 0;
            color: var(--brand-strong);
            font-size: 1rem;
            font-weight: 700;
        }

        .portal-name {
            margin: 0.2rem 0 0;
            color: var(--muted);
            font-size: 0.875rem;
        }

        .page-heading {
            max-width: 38rem;
            margin-bottom: 1.5rem;
        }

        h1 {
            margin: 0;
            color: var(--brand-strong);
            font-size: clamp(1.75rem, 5vw, 2.4rem);
            font-weight: 700;
            letter-spacing: -0.035em;
            line-height: 1.15;
        }

        .page-intro {
            margin: 0.65rem 0 0;
            color: var(--muted);
            font-size: 1rem;
        }

        .result-sheet {
            overflow-wrap: anywhere;
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            background: var(--surface);
        }

        .result-content {
            padding: clamp(1.25rem, 4vw, 2.25rem);
        }

        .decision {
            margin-bottom: 1.75rem;
            padding: 1rem 1.125rem;
            border: 1px solid transparent;
            border-radius: 0.5rem;
        }

        .decision--passed {
            border-color: #b7d7c1;
            background: var(--success-bg);
            color: var(--success-text);
        }

        .decision--failed {
            border-color: #e6bfbc;
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .decision--pending {
            border-color: #e8d59f;
            background: var(--warning-bg);
            color: var(--warning-text);
        }

        .decision-label,
        .field-label {
            display: block;
            margin: 0;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .decision-value {
            margin: 0.2rem 0 0;
            font-size: clamp(1.25rem, 4vw, 1.625rem);
            font-weight: 700;
            line-height: 1.25;
        }

        .student-name {
            margin: 0.25rem 0 0;
            color: var(--text);
            font-size: clamp(1.35rem, 4vw, 1.75rem);
            font-weight: 700;
            line-height: 1.3;
        }

        .student-details {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1rem;
            margin: 1.5rem 0 0;
            padding: 1.25rem 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .student-details div {
            min-width: 0;
        }

        .student-details dt {
            color: var(--muted);
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .student-details dd {
            margin: 0.2rem 0 0;
            color: var(--text);
            font-weight: 600;
        }

        .result-message {
            margin: 1.25rem 0 0;
            color: var(--text);
        }

        .verification {
            margin: 1.5rem 0 0;
            padding: 1rem;
            border-radius: 0.5rem;
            background: var(--surface-muted);
        }

        .verification-label {
            display: block;
            color: var(--muted);
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .verification code {
            display: block;
            margin-top: 0.2rem;
            color: var(--brand-strong);
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 0.875rem;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .result-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding: 1.25rem clamp(1.25rem, 4vw, 2.25rem);
            border-top: 1px solid var(--border);
        }

        .action-link {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            padding: 0.65rem 1rem;
            border: 1px solid var(--brand);
            border-radius: 0.375rem;
            background: var(--brand);
            color: #ffffff;
            font-size: 0.9375rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: background-color 140ms ease, border-color 140ms ease;
        }

        .action-link:hover {
            border-color: var(--brand-strong);
            background: var(--brand-strong);
        }

        .action-link:active {
            background: var(--brand-strong);
        }

        .action-link--secondary {
            background: transparent;
            color: var(--brand-strong);
        }

        .action-link--secondary:hover {
            background: var(--surface-muted);
        }

        .page-footer {
            margin-top: 1.25rem;
            color: var(--muted);
            font-size: 0.8125rem;
        }

        @media (min-width: 38rem) {
            .student-details {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1.5rem;
            }
        }

        @media (max-width: 34rem) {
            .page-shell {
                width: min(100% - 1.25rem, 56rem);
                padding-block: 1.25rem;
            }

            .result-actions {
                flex-direction: column;
            }

            .action-link {
                width: 100%;
            }
        }

        @media (prefers-color-scheme: dark) {
            :root {
                color-scheme: dark;
                --page: #111b15;
                --surface: #1b2820;
                --surface-muted: #24352a;
                --text: #edf2ee;
                --muted: #b7c3ba;
                --border: #3a4b3e;
                --brand: #34745d;
                --brand-strong: #d5eadb;
                --accent: #f1c777;
                --success-bg: #1d3b2a;
                --success-text: #c4e8ce;
                --warning-bg: #3a3018;
                --warning-text: #f5d98d;
                --danger-bg: #402524;
                --danger-text: #ffd1cc;
            }

            .decision--passed {
                border-color: #3d7050;
            }

            .decision--failed {
                border-color: #80504c;
            }

            .decision--pending {
                border-color: #776130;
            }

            .action-link {
                color: #ffffff;
            }

            .action-link--secondary {
                color: var(--brand-strong);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>
    <a class="skip-link" href="#main-content">Lewati ke hasil kelulusan</a>

    <div class="page-shell">
        <header class="school-header">
            <p class="school-name">SD Negeri Tomang 03 Pagi</p>
            <p class="portal-name">Portal pengumuman kelulusan</p>
        </header>

        <main id="main-content">
            <div class="page-heading">
                <h1>Hasil kelulusan</h1>
                <p class="page-intro">Informasi hasil berdasarkan data yang digunakan saat pencarian.</p>
            </div>

            <section class="result-sheet" aria-labelledby="student-name">
                <div class="result-content">
                    @if ($student->keterangan === 'LULUS')
                        <div class="decision decision--passed">
                            <p class="decision-label">Keputusan kelulusan</p>
                            <p class="decision-value">LULUS</p>
                        </div>
                    @elseif ($student->keterangan === 'TIDAK LULUS')
                        <div class="decision decision--failed">
                            <p class="decision-label">Keputusan kelulusan</p>
                            <p class="decision-value">TIDAK LULUS</p>
                        </div>
                    @else
                        <div class="decision decision--pending">
                            <p class="decision-label">Keputusan kelulusan</p>
                            <p class="decision-value">DITUNDA</p>
                        </div>
                    @endif

                    <p class="field-label">Nama siswa</p>
                    <h2 class="student-name" id="student-name">{{ $student->nama }}</h2>

                    <dl class="student-details">
                        <div>
                            <dt>NISN</dt>
                            <dd>{{ $student->nisn }}</dd>
                        </div>
                        <div>
                            <dt>Tempat, tanggal lahir</dt>
                            <dd>
                                @if ($student->tempat_lahir)
                                    {{ $student->tempat_lahir }},
                                @endif
                                <time datetime="{{ $birthDate->toDateString() }}">
                                    {{ $birthDate->locale('id')->translatedFormat('d F Y') }}
                                </time>
                            </dd>
                        </div>
                    </dl>

                    @if ($student->keterangan === 'LULUS')
                        <p class="result-message">
                            Berdasarkan keputusan rapat pleno Dewan Guru SD Negeri Tomang 03, siswa tersebut dinyatakan lulus.
                        </p>
                    @elseif ($student->keterangan === 'TIDAK LULUS')
                        <p class="result-message">
                            Untuk mendapatkan penjelasan dan informasi tindak lanjut, silakan hubungi wali kelas.
                        </p>
                    @else
                        <p class="result-message">
                            Keputusan kelulusan masih ditunda. Silakan hubungi wali kelas untuk memperoleh informasi lebih lanjut.
                        </p>
                    @endif

                    @if (session('secureNumber'))
                        <p class="verification">
                            <span class="verification-label">ID verifikasi</span>
                            <code>{{ session('secureNumber') }}</code>
                        </p>
                    @endif
                </div>

                <footer class="result-actions">
                    @if ($student->keterangan === 'LULUS')
                        <a class="action-link" href="{{ route('kelulusan.download', session('token')) }}">
                            Unduh Surat Keterangan Lulus (PDF)
                        </a>
                    @endif
                    <a class="action-link action-link--secondary" href="{{ route('pengumuman.index') }}">
                        Kembali ke portal kelulusan
                    </a>
                </footer>
            </section>

            <footer class="page-footer">
                Hasil ditampilkan berdasarkan data kelulusan SD Negeri Tomang 03 Pagi.
            </footer>
        </main>
    </div>
</body>

</html>
