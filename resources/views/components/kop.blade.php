@php
// LOGIKA BASE64 UNTUK GAMBAR (ANTI GAGAL DI PDF)
$logoPath = public_path('storage/jayakarta.png');
$logoBase64 = '';

if (file_exists($logoPath) && !is_dir($logoPath)) {
$type = pathinfo($logoPath, PATHINFO_EXTENSION);
$data = file_get_contents($logoPath);
$logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
}
@endphp

<style>
    .kop-table {
        width: 100%;
        border-bottom: 2.25px solid black;
        padding-bottom: 10px;
        margin-bottom: 5px;
        border-collapse: collapse;
    }

    .kop-table td {
        vertical-align: top;
    }

    .kop-logo-cell {
        width: 80px;
        padding-top: 5px;
    }

    .kop-logo-cell img {
        width: 80px;
        height: auto;
    }

    .kop-text-cell {
        text-align: center;
        padding: 0 10px;
    }

    .kop-spacer-cell {
        width: 20px;
    }

    .kop-text-cell h2 {
        margin: 0;
        font-size: 12pt;
        font-weight: bold;
        text-transform: uppercase;
        line-height: 1.2;
    }

    .kop-text-cell h3 {
        margin: 5px 0;
        font-size: 16pt;
        font-weight: bold;
        text-transform: uppercase;
        line-height: 1.2;
    }

    .kop-text-cell p {
        margin: 0;
        font-size: 11pt;
        line-height: 1.4;
    }
</style>

<table class="kop-table">
    <tbody>
        <tr>
            <td class="kop-logo-cell">
                {{-- Gunakan Base64 jika berhasil, gunakan asset() biasa sebagai cadangan (jika dibuka di web) --}}
                @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo Jakarta">
                @else
                <img src="{{ asset('storage/jayakarta.png') }}" alt="Logo Jakarta">
                @endif
            </td>
            <td class="kop-text-cell">
                <h2>PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA</h2>
                <h2>DINAS PENDIDIKAN</h2>
                <h3>SD NEGERI TOMANG 03</h3>
                <p>Jl. Gelong Baru No.29A, Kel. Tomang, Kec. Grogol Petamburan, Kota Jakarta Barat</p>
                <p>NPSN: 20101172, email: sdntomang03pagi@yahoo.co.id</p>
                <p style="text-align:right; font-size:10pt; margin-top:-15px; margin-right: -30px;">
                    Kode Pos: 11440
                </p>
            </td>
            <td class="kop-spacer-cell"></td>
        </tr>
    </tbody>
</table>