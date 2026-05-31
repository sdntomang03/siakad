<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen Kelulusan</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f1f5f9;
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
            max-width: 400px;
            padding: 2rem;
            border-radius: 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            text-align: center;
            margin: 20px;
        }

        .icon-check {
            width: 80px;
            height: 80px;
            background: #dcfce7;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .icon-check svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 1.5rem;
            color: #047857;
            margin: 0 0 0.5rem;
            font-weight: 700;
        }

        p {
            font-size: 0.9rem;
            color: #64748b;
            margin: 0 0 2rem;
            line-height: 1.5;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            color: #0f172a;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .info-value:last-child {
            margin-bottom: 0;
        }

        .footer-note {
            font-size: 0.8rem;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 1.5rem;
        }

        .footer-note strong {
            color: #475569;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="icon-check">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1>Dokumen Tervalidasi</h1>
        <p>Surat Keterangan Lulus ini <strong>ASLI</strong> dan diterbitkan secara resmi oleh sistem akademik sekolah.
        </p>

        <div class="info-box">
            <div class="info-label">Nama Siswa</div>
            <div class="info-value">{{ $data->nama }}</div>

            <div class="info-label">NISN</div>
            <div class="info-value">{{ $data->nisn }}</div>

            <div class="info-label">Status Kelulusan</div>
            <div class="info-value" style="color: #10b981;">{{ $data->keterangan }}</div>
        </div>

        <div class="footer-note">
            Dokumen ini ditandatangani secara elektronik oleh<br>
            <strong>Kepala SD Negeri Tomang 03 Pagi</strong>
        </div>
    </div>

</body>

</html>