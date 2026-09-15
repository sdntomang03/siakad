<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Galeri Foto Kelas {{ $classroom->nama_kelas }}</title>
    <style>
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
            margin: 0.4cm;
            text-align: center;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .photo-img {
            width: 4cm;
            height: 6cm;
            object-fit: cover;
            border: 1px solid #333;
        }

        /* CSS Khusus untuk kotak jika belum ada foto */
        .placeholder {
            width: 4cm;
            height: 6cm;
            border: 1px solid #333;
            background-color: #f3f4f6;
            color: #9ca3af;
            display: table;
            font-size: 11px;
            font-weight: bold;
            margin: 0 auto;
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

            @php
            // Logika Base64 untuk mencegah DomPDF loading selamanya
            $imagePath = $student->foto ? public_path('storage/' . $student->foto) : null;
            $imageBase64 = null;

            if ($imagePath && file_exists($imagePath)) {
            $type = pathinfo($imagePath, PATHINFO_EXTENSION);
            // Ambil isi file dan ubah ke base64
            $data = file_get_contents($imagePath);
            $imageBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            @endphp

            @if($imageBase64)
            <!-- Tampilkan gambar dari string Base64 -->
            <img src="{{ $imageBase64 }}" class="photo-img">
            @else
            <!-- Tampilan jika tidak ada foto / file fisik tidak ditemukan -->
            <div class="placeholder">
                <span>BELUM ADA FOTO</span>
            </div>
            @endif

            <div class="student-name">{{ $student->nama_lengkap }}</div>
        </div>
        @endforeach
    </div>
</body>

</html>