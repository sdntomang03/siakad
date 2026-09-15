<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Galeri Foto Siswa</title>
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
        <p style="margin:0; font-size:12px;">Tahun Ajaran: {{ $classroom->academicYear->tahun_ajaran ?? '-' }}</p>
    </div>

    <div class="gallery">
        @foreach($students as $student)
        <div class="photo-item">

            @php
            $imagePath = $student->foto ? public_path('storage/' . $student->foto) : null;
            $imageBase64 = null;

            if ($imagePath && file_exists($imagePath)) {
            try {
            // Baca file gambar asli (WebP/PNG/JPG)
            $imageString = file_get_contents($imagePath);
            $im = @imagecreatefromstring($imageString);

            if ($im !== false) {
            $width = imagesx($im);
            $height = imagesy($im);

            // RESIZE: Perkecil resolusi menjadi lebar 200px (Sangat cukup tajam untuk cetak 4x6 cm)
            // Ini akan memangkas beban DomPDF hingga 90%
            $newWidth = 200;
            $newHeight = floor($height * ($newWidth / $width));

            $thumb = imagecreatetruecolor($newWidth, $newHeight);

            // Beri background putih (mencegah error hitam jika gambar asli transparan)
            $white = imagecolorallocate($thumb, 255, 255, 255);
            imagefill($thumb, 0, 0, $white);

            // Proses kompresi gambar
            imagecopyresampled($thumb, $im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Output paksa ke format JPEG agar DomPDF tidak perlu memproses WebP
            ob_start();
            imagejpeg($thumb, null, 75); // Kualitas 75%
            $resizedData = ob_get_clean();

            $imageBase64 = 'data:image/jpeg;base64,' . base64_encode($resizedData);

            imagedestroy($im);
            imagedestroy($thumb);
            }
            } catch (\Exception $e) {
            $imageBase64 = null;
            }
            }
            @endphp

            @if($imageBase64)
            <img src="{{ $imageBase64 }}" class="photo-img">
            @else
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