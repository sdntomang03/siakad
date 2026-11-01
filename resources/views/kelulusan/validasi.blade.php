<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen Kelulusan</title>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f6f2;
            color: #1e293b;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            background: #fff;
            width: 100%;
            max-width: 420px;
            padding: 2rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px -12px rgba(15, 23, 42, 0.24);
            text-align: center;
            margin: 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        /* Pita hijau di atas kartu */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: #166534;
        }

        /* Ikon Perisai (Shield) */
        .icon-shield {
            width: 80px;
            height: 80px;
            background: #f0fdf4;
            color: #166534;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .icon-shield svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 1.4rem;
            color: #0f172a;
            margin: 0 0 0.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        /* Badge Terverifikasi */
        .badge-secure {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #047857;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 2rem;
            border: 1px solid #a7f3d0;
        }

        .badge-secure svg {
            width: 14px;
            height: 14px;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            color: #0f172a;
            font-weight: 600;
            margin-bottom: 1.25rem;
            position: relative;
            z-index: 1;
        }

        .info-value:last-child {
            margin-bottom: 0;
        }

        .nomor-validasi {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            color: #166534;
            letter-spacing: 0.5px;
            font-size: 1.05rem;
            font-weight: 700;
            background: #dcfce7;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 4px;
        }

        .status-lulus {
            color: #047857;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .footer-note {
            font-size: 0.75rem;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 1.5rem;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .footer-note strong {
            color: #334155;
        }

        .portal-link {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            margin-top: 12px;
            color: #255b4b;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .portal-link:focus-visible {
            outline: 3px solid #8a5e13;
            outline-offset: 3px;
        }

        .lock-icon {
            color: #94a3b8;
            width: 18px;
            height: 18px;
        }

        @media (max-width: 480px) {
            .card {
                padding: 1.5rem 1rem;
            }

            .info-box {
                padding: 1rem;
            }

            .nomor-validasi {
                max-width: 100%;
                overflow-wrap: anywhere;
            }
        }
    </style>
</head>

<body>

    <main class="card">
        <header>
            <div class="icon-shield" aria-hidden="true">
            <svg aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>

            <h1>Dokumen Asli</h1>

            <div class="badge-secure">
            <svg aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M12 1L9 4l-4-1-1 4-4 3 3 4-3 4 4 1 1 4 4-1 3 3 3-3 4 1 1-4 4-3-3-4 3-4-4-1-1-4-4 1zM10.8 16.6l-4.2-4.2 1.4-1.4 2.8 2.8 5.6-5.6 1.4 1.4-7 7z" />
            </svg>
            Tervalidasi Sistem
            </div>
        </header>

        <div class="info-box">
            <div class="info-label">ID Verifikasi Digital</div>
            <div class="info-value">
                <span class="nomor-validasi">{{ $secureNumber }}</span>
            </div>

            <div class="info-label">Atas Nama</div>
            <div class="info-value">{{ strtoupper($data->nama) }}</div>

            <div class="info-label">NISN Siswa</div>
            <div class="info-value">{{ $data->nisn }}</div>
            <div class="info-label">Tempat, Tanggal Lahir</div>
            <div class="info-value">{{ $data->tempat_lahir }}, {{
                \Carbon\Carbon::parse($data->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</div>

            <div class="info-label">Status Ketetapan</div>
            <div class="info-value status-lulus">
                <svg aria-hidden="true" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
                {{ $data->keterangan }}
            </div>
            <div class="info-label">Nomor SKL</div>
            <div class="info-value">{{ $data->nomor_skl }}</div>
        </div>

        <footer>
        <div class="footer-note">
            <svg class="lock-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span>
                Tanda tangan elektronik telah dienkripsi.<br>
                Diterbitkan secara sah oleh<br>
                <strong>Kepala SD Negeri Tomang 03 Pagi</strong>
            </span>
        </div>
            <nav aria-label="Navigasi portal kelulusan">
                <a class="portal-link" href="{{ route('pengumuman.index') }}">Kembali ke portal kelulusan</a>
            </nav>
        </footer>
    </main>

</body>

</html>