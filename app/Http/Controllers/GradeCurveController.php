<?php

namespace App\Http\Controllers;

use App\Exports\AdjustedGradesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Tambahkan library ini untuk mengambil huruf Kolom Excel

class GradeCurveController extends Controller
{
    public function index()
    {
        return view('admin.katrol_nilai');
    }

    public function process(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv',
            'kkm' => 'required|numeric|min:0',
            'target_max' => 'required|numeric|max:100',
        ]);

        $kkm = $request->kkm;
        $targetMax = $request->target_max;

        // Membaca file Excel
        $importArray = Excel::toArray(new class implements ToArray
        {
            public function array(array $array) {}
        }, $request->file('file_excel'));

        $rows = $importArray[0];

        // Pisahkan Header
        $headers = array_shift($rows);
        $originalColumnCount = count($headers); // Simpan jumlah kolom asli untuk perhitungan range Excel

        // Tambahkan header baru
        $headers[] = 'RATA-RATA';
        $headers[] = 'PERINGKAT';

        $adjustedData = [];
        $adjustedData[] = $headers;

        // DETEKSI KOORDINAT KOLOM EXCEL SECARA DINAMIS
        // PhpSpreadsheet menggunakan index base-1 (1=A, 2=B, 3=C)
        // Nilai dimulai dari kolom ke-3 (Kolom C)
        $startGradeColLetter = Coordinate::stringFromColumnIndex(3);

        // Kolom nilai terakhir adalah batas jumlah kolom asli sebelum ditambah rata-rata
        $endGradeColLetter = Coordinate::stringFromColumnIndex($originalColumnCount);

        // Kolom Rata-rata terletak tepat setelah kolom nilai terakhir
        $avgColLetter = Coordinate::stringFromColumnIndex($originalColumnCount + 1);

        // Total baris di Excel (termasuk baris Header ke-1)
        $lastRowExcel = count($rows) + 1;

        $subjectStartIndex = 2;

        // LANGKAH A: Cari Nilai Min & Max per Mata Pelajaran (untuk rumus Katrol PHP)
        $minMaxPerSubject = [];
        for ($col = $subjectStartIndex; $col < $originalColumnCount; $col++) {
            $scores = array_column($rows, $col);
            $scores = array_filter($scores, 'is_numeric');

            if (count($scores) > 0) {
                $minMaxPerSubject[$col] = [
                    'min' => min($scores),
                    'max' => max($scores),
                ];
            }
        }

        // LANGKAH B: Hitung Katrol dan Suntikkan Rumus Excel
        foreach ($rows as $rowIndex => $row) {
            $newRow = $row;
            $excelRowNumber = $rowIndex + 2; // Data siswa dimulai dari Baris ke-2 di Excel

            foreach ($minMaxPerSubject as $colIndex => $bounds) {
                $nilaiAsli = $row[$colIndex];

                if (is_numeric($nilaiAsli)) {
                    $min = $bounds['min'];
                    $max = $bounds['max'];

                    if ($max == $min) {
                        $newRow[$colIndex] = $targetMax;
                    } else {
                        // Rumus Katrol Linear
                        $nilaiBaru = $kkm + (($nilaiAsli - $min) / ($max - $min)) * ($targetMax - $kkm);
                        $newRow[$colIndex] = round($nilaiBaru);
                    }
                }
            }

            $newRow[] = '=AVERAGE('.$startGradeColLetter.$excelRowNumber.':'.$endGradeColLetter.$excelRowNumber.')';

            // Rumus: =RANK(F2, F$2:F$32, 0)
            $newRow[] = '=RANK('.$avgColLetter.$excelRowNumber.', '.$avgColLetter.'$2:'.$avgColLetter.'$'.$lastRowExcel.', 0)';

            $adjustedData[] = $newRow;
        }

        // LANGKAH C: Unduh Hasilnya
        return Excel::download(new AdjustedGradesExport($adjustedData), 'Hasil_Katrol_Otomatis_'.date('Ymd_His').'.xlsx');
    }
}
