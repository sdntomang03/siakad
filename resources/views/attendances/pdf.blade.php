<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        h2,
        h3 {
            text-align: center;
            margin: 5px 0;
        }

        .info {
            margin-bottom: 15px;
            font-weight: bold;
        }

        /* Tabel Utama */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #333;
            padding: 3px;
            text-align: center;
        }

        .text-left {
            text-align: left !important;
        }

        .nama-siswa {
            width: 120px;
            white-space: nowrap;
        }

        .day-off {
            background-color: #ffcccc;
        }

        /* Warna Status */
        .hadir {
            color: #047857;
            font-weight: bold;
        }

        .sakit {
            color: #d97706;
            font-weight: bold;
        }

        .izin {
            color: #2563eb;
            font-weight: bold;
        }

        .alfa {
            color: #e11d48;
            font-weight: bold;
        }

        .dot {
            color: #047857;
            font-weight: bold;
            font-size: 12px;
        }

        .rekap-col {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .rekap-s {
            color: #d97706;
        }

        .rekap-i {
            color: #2563eb;
        }

        .rekap-a {
            color: #e11d48;
        }

        /* CSS Fraksi */
        table.table-wrapper {
            border-collapse: collapse;
            border: none;
            margin: 0;
        }

        table.table-wrapper td {
            border: none !important;
            vertical-align: middle !important;
            padding: 0 5px !important;
            font-size: 11px;
        }

        table.table-fraction {
            border-collapse: collapse;
            text-align: center;
            margin: 0;
        }

        table.table-fraction td {
            border: none !important;
            padding: 1px 4px !important;
            line-height: 1.1;
        }

        table.table-fraction td.fraction-top {
            border-bottom: 1px solid #000 !important;
        }

        /* CSS Tabel Tanda Tangan */
        table.signature-table {
            width: 100%;
            border: none;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        table.signature-table td {
            border: none !important;
            text-align: center;
            font-size: 11px;
            vertical-align: top;
            width: 50%;
        }
    </style>
</head>

<body>

    <h2>REKAPITULASI ABSENSI BULANAN</h2>
    <h3>{{ $selectedClassroom->nama_kelas }}</h3>
    <h3>Periode: {{ $periode }}</h3>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No</th>
                <th rowspan="2" class="nama-siswa">Nama Siswa</th>
                @foreach($dates as $day => $info)
                @php $isDayOff = $info['is_weekend'] || $info['is_holiday']; @endphp
                <th class="{{ $isDayOff ? 'day-off' : '' }}" style="font-size: 8px;">
                    {{ $info['day_name'] }}
                </th>
                @endforeach
                <th rowspan="2" class="rekap-col rekap-s" style="width: 20px;">S</th>
                <th rowspan="2" class="rekap-col rekap-i" style="width: 20px;">I</th>
                <th rowspan="2" class="rekap-col rekap-a" style="width: 20px;">A</th>
            </tr>
            <tr>
                @foreach($dates as $day => $info)
                @php $isDayOff = $info['is_weekend'] || $info['is_holiday']; @endphp
                <th class="{{ $isDayOff ? 'day-off' : '' }}">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @php
            $grandHadir = 0;
            $grandSakit = 0;
            $grandIzin = 0;
            $grandAlfa = 0;
            @endphp

            @foreach($students as $index => $student)
            @php
            $totalS = 0;
            $totalI = 0;
            $totalA = 0;

            foreach($dates as $day => $info) {
            $status = $attendanceData[$student->id][$day] ?? null;
            if($status == 'hadir') $grandHadir++;
            elseif($status == 'sakit') { $totalS++; $grandSakit++; }
            elseif($status == 'izin') { $totalI++; $grandIzin++; }
            elseif($status == 'alfa') { $totalA++; $grandAlfa++; }
            }
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left nama-siswa">{{ $student->nama_lengkap }}</td>

                @foreach($dates as $day => $info)
                @php
                $status = $attendanceData[$student->id][$day] ?? null;
                $isDayOff = $info['is_weekend'] || $info['is_holiday'];

                $label = '-';
                $class = '';

                if($status == 'hadir') {
                $label = 'H';
                $class = 'hadir';
                } elseif($status == 'sakit') {
                $label = 'S';
                $class = 'sakit';
                } elseif($status == 'izin') {
                $label = 'I';
                $class = 'izin';
                } elseif($status == 'alfa') {
                $label = 'A';
                $class = 'alfa';
                } elseif(!$isDayOff && !$status) {
                $label = 'H';
                $class = 'dot';
                } elseif($isDayOff && !$status) {
                $label = 'L';
                $class = 'alfa';
                }
                @endphp
                <td class="{{ $isDayOff ? 'day-off' : '' }}">
                    <span class="{{ $class }}">{{ $label }}</span>
                </td>
                @endforeach

                <td class="rekap-col rekap-s">{{ $totalS > 0 ? $totalS : '-' }}</td>
                <td class="rekap-col rekap-i">{{ $totalI > 0 ? $totalI : '-' }}</td>
                <td class="rekap-col rekap-a">{{ $totalA > 0 ? $totalA : '-' }}</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            @php
            $jumlahSiswa = count($students);
            $hariEfektif = 0;

            foreach($dates as $info) {
            if(!$info['is_weekend'] && !$info['is_holiday']) $hariEfektif++;
            }

            $penyebut = $jumlahSiswa * $hariEfektif;
            $persenSakit = $penyebut > 0 ? round(($grandSakit / $penyebut) * 100, 1) : 0;
            $persenIzin = $penyebut > 0 ? round(($grandIzin / $penyebut) * 100, 1) : 0;
            $persenAlfa = $penyebut > 0 ? round(($grandAlfa / $penyebut) * 100, 1) : 0;
            $totalKolomSisa = count($dates) + 3;
            @endphp

            <tr>
                <td colspan="2" class="text-left" style="font-weight: bold; color: #d97706;">Persentase Sakit (S)</td>
                <td colspan="{{ $totalKolomSisa }}" class="text-left sakit" style="padding-left: 10px;">
                    <table class="table-wrapper">
                        <tr>
                            <td>
                                <table class="table-fraction">
                                    <tr>
                                        <td class="fraction-top">{{ $grandSakit }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $jumlahSiswa }} &times; {{ $hariEfektif }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>&times; 100% = {{ $persenSakit }}%</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="text-left" style="font-weight: bold; color: #2563eb;">Persentase Izin (I)</td>
                <td colspan="{{ $totalKolomSisa }}" class="text-left izin" style="padding-left: 10px;">
                    <table class="table-wrapper">
                        <tr>
                            <td>
                                <table class="table-fraction">
                                    <tr>
                                        <td class="fraction-top">{{ $grandIzin }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $jumlahSiswa }} &times; {{ $hariEfektif }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>&times; 100% = {{ $persenIzin }}%</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="text-left" style="font-weight: bold; color: #e11d48;">Persentase Alfa (A)</td>
                <td colspan="{{ $totalKolomSisa }}" class="text-left alfa" style="padding-left: 10px;">
                    <table class="table-wrapper">
                        <tr>
                            <td>
                                <table class="table-fraction">
                                    <tr>
                                        <td class="fraction-top">{{ $grandAlfa }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $jumlahSiswa }} &times; {{ $hariEfektif }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>&times; 100% = {{ $persenAlfa }}%</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- KALKULASI HARI KERJA TERAKHIR -->
    @php
    // Mencari hari terakhir pada bulan tersebut
    $lastDayOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();

    // Memundurkan tanggal jika jatuh pada Sabtu (Weekend) atau Minggu
    while ($lastDayOfMonth->isWeekend()) {
    $lastDayOfMonth->subDay();
    }

    $tanggalTtd = $lastDayOfMonth->locale('id')->isoFormat('D MMMM YYYY');
    @endphp

    <!-- TABEL TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah
                <br><br><br><br><br>
                <strong><u>{{ $sekolah->kepala_sekolah ?? '(..........................................)'
                        }}</u></strong><br>
                NIP. {{ $sekolah->nip ?? '..........................................' }}
            </td>
            <td>
                Jakarta, {{ $tanggalTtd }}<br>
                Guru Kelas {{ $selectedClassroom->tingkat }}{{ $selectedClassroom->nama_kelas }}
                <br><br><br><br><br>
                <strong><u>{{ $selectedClassroom->homeroomTeacher->nama_lengkap ??
                        '(..........................................)' }}</u></strong><br>
                NIP. {{ $selectedClassroom->homeroomTeacher->nip ?? '..........................................' }}
            </td>
        </tr>
    </table>

</body>

</html>