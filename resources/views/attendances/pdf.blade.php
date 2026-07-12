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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 3px;
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .nama-siswa {
            width: 120px;
            white-space: nowrap;
        }

        .day-off {
            background-color: #ffcccc;
        }

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
    </style>
</head>

<body>

    <h2>REKAPITULASI ABSENSI BULANAN</h2>
    <h3>Kelas: {{ $selectedClassroom->tingkat }} - {{ $selectedClassroom->nama_kelas }}</h3>
    <h3>Periode: {{ $periode }}</h3>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No</th>
                <th rowspan="2" class="nama-siswa">Nama Siswa</th>
                @foreach($dates as $day => $info)
                @php $isDayOff = $info['is_weekend'] || $info['is_holiday']; @endphp
                <th class="{{ $isDayOff ? 'day-off' : '' }}" style="font-size: 8px;">{{ $info['day_name'] }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($dates as $day => $info)
                @php $isDayOff = $info['is_weekend'] || $info['is_holiday']; @endphp
                <th class="{{ $isDayOff ? 'day-off' : '' }}">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
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
                $label = '•';
                $class = 'dot';
                } elseif($isDayOff && !$status) {
                $label = 'L';
                $class = 'alfa'; // Gunakan warna merah untuk label L
                }
                @endphp
                <td class="{{ $isDayOff ? 'day-off' : '' }}">
                    <span class="{{ $class }}">{{ $label }}</span>
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-left" style="font-weight: bold; color: #d97706;">Total Sakit (S)</td>
                @foreach($dates as $day => $info)
                @php
                $countS = 0;
                foreach($students as $student) {
                if(($attendanceData[$student->id][$day] ?? null) == 'sakit') $countS++;
                }
                @endphp
                <td class="{{ ($info['is_weekend'] || $info['is_holiday']) ? 'day-off' : '' }} sakit">
                    {{ $countS > 0 ? $countS : '-' }}
                </td>
                @endforeach
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="font-weight: bold; color: #2563eb;">Total Izin (I)</td>
                @foreach($dates as $day => $info)
                @php
                $countI = 0;
                foreach($students as $student) {
                if(($attendanceData[$student->id][$day] ?? null) == 'izin') $countI++;
                }
                @endphp
                <td class="{{ ($info['is_weekend'] || $info['is_holiday']) ? 'day-off' : '' }} izin">
                    {{ $countI > 0 ? $countI : '-' }}
                </td>
                @endforeach
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="font-weight: bold; color: #e11d48;">Total Alfa (A)</td>
                @foreach($dates as $day => $info)
                @php
                $countA = 0;
                foreach($students as $student) {
                if(($attendanceData[$student->id][$day] ?? null) == 'alfa') $countA++;
                }
                @endphp
                <td class="{{ ($info['is_weekend'] || $info['is_holiday']) ? 'day-off' : '' }} alfa">
                    {{ $countA > 0 ? $countA : '-' }}
                </td>
                @endforeach
            </tr>
        </tfoot>
    </table>

</body>

</html>