<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Galeri Foto Kelas {{ $classroom->nama_kelas }}</title>
    <style>
        /* Ukuran kertas A4 standar */
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.2cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 14px;
        }

        .gallery {
            text-align: center;
            width: 100%;
        }

        .photo-item {
            display: inline-block;
            width: 4cm;
            /* Lebar total item = ukuran pas foto */
            margin: 0.4cm;
            /* Jarak antar foto */
            text-align: center;
            vertical-align: top;
            page-break-inside: avoid;
            /* Mencegah foto terpotong di akhir halaman */
        }

        .photo-img {
            width: 4cm;
            /* Lebar pas foto */
            height: 6cm;
            /* Tinggi pas foto */
            object-fit: cover;
            border: 1px solid #333;
        }

        .placeholder {
            width: 4cm;
            height: 6cm;
            border: 1px solid #333;
            background-color: #f3f4f6;
            color: #9ca3af;
            display: table;
            font-size: 12px;
        }

        .placeholder span {
            display: table-cell;
            vertical-align: middle;
        }

        .student-name {
            font-size: 11px;
            margin-top: 5px;
            font-weight: bold;
            text-transform: uppercase;
            word-wrap: break-word;
            line-height: 1.2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Galeri Foto Kelas {{ $classroom->nama_kelas }}</h2>
        <p>Tahun Ajaran: {{ $classroom->academicYear->tahun_ajaran ?? '-' }}</p>
    </div>

    <div class="gallery">
        @foreach($classroom->students as $student)
        <div class="photo-item">
            <!-- Gunakan public_path() agar library DOMPDF dapat mengakses file gambar lokal -->
            @if($student->foto && file_exists(public_path('storage/' . $student->foto)))
            <img src="{{ public_path('storage/' . $student->foto) }}" class="photo-img">
            @else
            <div class="placeholder">
                <span>Tanpa Foto</span>
            </div>
            @endif
            <div class="student-name">{{ $student->nama_lengkap }}</div>
        </div>
        @endforeach
    </div>
</body>

</html>