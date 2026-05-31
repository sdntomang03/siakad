<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Lulus - {{ $data->nama }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            font-size: 12pt;
            line-height: 1.5;
            padding: 20px 40px;
        }

        .judul-surat {
            text-align: center;
            margin-bottom: 30px;
            margin-top: 10px;
        }

        .judul-surat h3 {
            text-decoration: underline;
            margin: 0;
            font-size: 14pt;
        }

        /* Styling untuk Nomor Surat */
        .judul-surat p.nomor-surat {
            margin: 5px 0 0 0;
            font-size: 12pt;
        }

        .tabel-biodata {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 20px;
        }

        .tabel-biodata td {
            padding: 5px 0;
            vertical-align: top;
        }

        .tabel-biodata td:first-child {
            width: 30%;
        }

        .tabel-biodata td:nth-child(2) {
            width: 2%;
        }

        .paragraf {
            text-indent: 40px;
            text-align: justify;
            margin-bottom: 15px;
        }

        .ttd-box {
            width: 100%;
            margin-top: 40px;
        }

        /* Penyesuaian layout tanda tangan dan QR */
        .ttd-kiri {
            float: left;
            width: 45%;
            text-align: left;
            font-size: 9pt;
            color: #555;
            padding-top: 30px;
        }

        .ttd-kanan {
            float: right;
            width: 45%;
            text-align: left;
            position: relative;
        }

        .clear {
            clear: both;
        }

        .qr-box {
            margin: 15px 0;
        }

        .qr-img {
            width: 65px;
            height: 65px;
            border: 1px solid #ccc;
            padding: 2px;
        }
    </style>
</head>

<body>

    <x-kop />

    <div class="judul-surat">
        <h3>SURAT KETERANGAN LULUS</h3>
        <p class="nomor-surat">Nomor: {{ $data->nomor_skl }}</p>
    </div>

    <p class="paragraf">Kepala SD Negeri Tomang 03 Pagi selaku Ketua Penyelenggara Ujian Sekolah Tahun Pelajaran
        2025/2026 berdasarkan kriteria kelulusan dari Satuan Pendidikan, menerangkan bahwa:</p>

    <table class="tabel-biodata">
        <tr>
            <td>Nama Siswa</td>
            <td>:</td>
            <td><strong>{{ $data->nama }}</strong></td>
        </tr>
        <tr>
            <td>NISN</td>
            <td>:</td>
            <td>{{ $data->nisn }}</td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $data->tempat_lahir }}, {{
                \Carbon\Carbon::parse($data->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <p class="paragraf">Telah mengikuti serangkaian evaluasi akademik Tahun Pelajaran 2025/2026 dan berdasarkan hasil
        keputusan Rapat Pleno Dewan Guru, siswa tersebut dinyatakan:</p>

    <h2 style="text-align: center; font-size: 24pt; color: #000; letter-spacing: 5px; margin: 25px 0;">LULUS</h2>

    <p class="paragraf">Surat keterangan ini bersifat sementara dan dapat digunakan sebagai pengganti Ijazah asli yang
        masih dalam proses penerbitan. Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <div class="ttd-box">
        <div class="ttd-kiri">
        </div>
        <div class="ttd-kanan">
            <p>Jakarta, {{ \Carbon\Carbon::parse('2026-06-02')->locale('id')->translatedFormat('d F Y') }}<br>Kepala
                Sekolah,</p>

            <div class="qr-box">
                <img class="qr-img" src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
            </div>

            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">Limah Yuhana, S. Pd. MM</p>
            <p style="margin: 0;">NIP. 196805051993032010</p>
        </div>
        <div class="clear"></div>
    </div>

</body>

</html>