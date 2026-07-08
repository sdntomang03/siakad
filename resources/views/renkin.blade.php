<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rencana Kinerja Helper - Guru Kelas SD</title>
    <style>
        :root {
            --blue-main: #1e40af;
            --blue-bg: #eff6ff;
            --blue-border: #bfdbfe;
            --green-main: #15803d;
            --green-bg: #f0fdf4;
            --green-border: #bbf7d0;
            --amber-main: #b45309;
            --amber-bg: #fefce8;
            --amber-border: #fef08a;
            --pink-main: #be185d;
            --pink-bg: #fdf2f8;
            --pink-border: #fbcfe8;
            --purple-main: #6d28d9;
            --purple-bg: #f5f3ff;
            --purple-border: #ddd6fe;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 24px;
            color: var(--text-dark);
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 6px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        /* --- TABS NAVIGATION --- */
        .tabs-container {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 10px 18px;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            color: var(--text-muted);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            flex: 1 1 auto;
            text-align: center;
        }

        .tab-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .tab-btn.active {
            background: var(--blue-main);
            color: #fff;
            border-color: var(--blue-main);
            box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.2);
        }

        /* --- SEARCH BOX --- */
        .search-box {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            margin-bottom: 24px;
            outline: none;
            transition: border-color 0.15s;
            user-select: auto;
        }

        .search-box:focus {
            border-color: var(--blue-main);
        }

        /* --- HIERARCHY TREE LAYOUT --- */
        .tree-node {
            position: relative;
            padding-left: 24px;
            margin-bottom: 12px;
        }

        .tree-node::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #cbd5e1;
        }

        .tree-node:last-child::before {
            height: 24px;
            bottom: auto;
        }

        .tree-node::after {
            content: '';
            position: absolute;
            left: 6px;
            top: 24px;
            width: 14px;
            height: 2px;
            background: #cbd5e1;
        }

        /* --- CARD ELEMENTS --- */
        .block-item {
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.15s;
        }

        .block-header {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action-label {
            font-size: 10px;
            background: rgba(0, 0, 0, 0.06);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* --- COLOR CODED LEVELS --- */
        .level-root {
            border-left: 6px solid var(--blue-main);
            background: #ffffff;
            border-color: #94a3b8;
        }

        .level-root .block-header {
            color: var(--blue-main);
        }

        .level-ra {
            border-left: 5px solid var(--green-main);
            background: var(--green-bg);
            border-color: var(--green-border);
        }

        .level-ra .block-header {
            color: var(--green-main);
        }

        .level-kk {
            border-left: 5px solid var(--amber-main);
            background: var(--amber-bg);
            border-color: var(--amber-border);
        }

        .level-kk .block-header {
            color: var(--amber-main);
        }

        .level-out {
            border-left: 5px solid var(--pink-main);
            background: var(--pink-bg);
            border-color: var(--pink-border);
        }

        .level-out .block-header {
            color: var(--pink-main);
        }

        .level-lampiran {
            border-left: 5px solid var(--purple-main);
            background: var(--purple-bg);
            border-color: var(--purple-border);
        }

        .level-lampiran .block-header {
            color: var(--purple-main);
        }

        /* --- CLICKABLE ROW STYLING --- */
        .clickable-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 6px;
            cursor: pointer;
            transition: all 0.1s;
        }

        .clickable-row:last-child {
            margin-bottom: 0;
        }

        .clickable-row:hover {
            border-color: #3b82f6;
            background: #f8fafc;
            transform: translateX(2px);
        }

        .row-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            width: 140px;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .row-value {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-dark);
            flex: 1;
            padding-right: 12px;
            line-height: 1.4;
        }

        .copy-status {
            font-size: 11px;
            color: #16a34a;
            font-weight: 700;
            opacity: 0;
            transition: opacity 0.15s;
            flex-shrink: 0;
        }

        .clickable-row.copied {
            background: #dcfce7 !important;
            border-color: #4ade80 !important;
        }

        .clickable-row.copied .copy-status {
            opacity: 1;
        }

        /* Target Grid Layout */
        .target-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-top: 6px;
        }

        .target-grid .clickable-row {
            flex-direction: column;
            text-align: center;
            padding: 8px 6px;
        }

        .target-grid .row-label {
            width: auto;
            margin-bottom: 4px;
        }

        .target-grid .row-value {
            padding: 0;
            font-weight: 700;
            font-size: 14px;
        }

        /* Split Row Layout */
        .split-row {
            display: flex;
            gap: 8px;
            margin-bottom: 6px;
        }

        .split-row .clickable-row {
            flex: 1;
            margin-bottom: 0;
        }

        /* Chips for output targets */
        .chip-box {
            display: flex;
            gap: 6px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .chip-item {
            font-size: 11px;
            font-weight: 600;
            padding: 6px 12px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.1s;
        }

        .chip-item:hover {
            border-color: #3b82f6;
            background: var(--blue-bg);
        }

        .chip-item.copied {
            background: #dcfce7;
            border-color: #4ade80;
        }

        /* Lampiran numbered rows */
        .lampiran-num {
            width: 26px;
            height: 26px;
            flex-shrink: 0;
            border-radius: 50%;
            background: var(--purple-main);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .lampiran-row {
            display: flex;
            align-items: center;
        }

        .lampiran-row .row-value {
            flex: 1;
        }

        /* Toast Alert */
        .toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 9999;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .toast.show {
            opacity: 1;
        }

        /* =========================================
     RESPONSIVE DESIGN (MOBILE & TABLET)
     ========================================= */
        @media (max-width: 650px) {
            body {
                padding: 16px 12px;
            }

            h1 {
                font-size: 20px;
            }

            .subtitle {
                font-size: 13px;
            }

            .tab-btn {
                font-size: 12px;
                padding: 8px 12px;
            }

            .clickable-row {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px;
            }

            .row-label {
                width: 100%;
                margin-bottom: 4px;
                font-size: 10px;
            }

            .row-value {
                width: 100%;
                padding-right: 0;
                font-size: 13px;
            }

            .copy-status {
                position: absolute;
                right: 10px;
                top: 10px;
            }

            .clickable-row {
                position: relative;
            }

            .lampiran-row.clickable-row {
                flex-direction: row;
                align-items: center;
            }

            .lampiran-row .row-value {
                width: auto;
            }

            .split-row {
                flex-direction: column;
                gap: 6px;
            }

            .split-row .clickable-row {
                width: 100%;
            }

            .target-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 6px;
            }

            .target-grid .copy-status {
                display: none;
            }

            .target-grid .clickable-row {
                align-items: center;
            }

            .tree-node {
                padding-left: 14px;
            }

            .tree-node::before {
                left: 4px;
            }

            .tree-node::after {
                left: 4px;
                width: 10px;
            }

            .block-item {
                padding: 12px 10px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>📋 Rencana Kinerja Helper - Guru Kelas SD</h1>
        <p class="subtitle">Klik tab di bawah untuk berpindah antar Rencana Kinerja (Indikator Utama) berdasarkan file
            Excel Renkin Guru.</p>

        <div class="tabs-container">
            <button class="tab-btn active" onclick="switchTab(0)" id="tab-0">1. Perencanaan</button>
            <button class="tab-btn" onclick="switchTab(1)" id="tab-1">2. Pelaksanaan</button>
            <button class="tab-btn" onclick="switchTab(2)" id="tab-2">3. Evaluasi Hasil</button>
            <button class="tab-btn" onclick="switchTab(3)" id="tab-3">4. Pembimbingan & Tugas Tambahan</button>
            <button class="tab-btn" onclick="switchTab(4)" id="tab-4">5. Lampiran SKP</button>
        </div>

        <input type="text" class="search-box" id="searchBox" placeholder="Cari nama dokumen pada halaman ini...">

        <div id="treeContainer"></div>
    </div>

    <div class="toast" id="toast">Tersalin ke clipboard</div>

    <script>
        // DATA STRUKTUR HIERARKI LENGKAP BERSUMBER DARI EXCEL RENKIN
  const TREE_DATA = [
    // --- RENCANA KINERJA 1 ---
    {
      id: "RHK-1",
      title: "1. Terlaksananya Supervisi Guru dan Tenaga Kependidikan",
      rencanaHasilKerja: "Dokumen perencanaan pembelajaran / pembimbingan yang tersusun",
      indikatorKinerja: "Jumlah dokumen perencanaan pembelajaran / pembimbingan yang tersusun",
      targetTahunan: "7",
      satuan: "Dokumen",
      rumusRealisasi: "AKUMULASI",
      rumusCapaian: "NORMAL",
      tw1: "2", tw2: "1", tw3: "3", tw4: "1",
      renaksi: [
        {
          id: "RA-1",
          raText: "Menyusun Dokumen Program Tahunan",
          kriteria: {
            id: "KK-1.1",
            kkText: "Tersusunnya Dokumen Program Tahunan",
            outputs: [
              { id: "OUT-1.1.1", oText: "Program Tahunan", triwulan: "Triwulan 3", target: "1", satuan: "Dokumen", labelCode: "T/O 1.1.1" }
            ]
          }
        },
        {
          id: "RA-2",
          raText: "Menyusun Dokumen Program Semester",
          kriteria: {
            id: "KK-2.1",
            kkText: "Tersusunnya Dokumen Program Semester",
            outputs: [
              { id: "OUT-2.1.1", oText: "Program Semester", triwulan: "Triwulan 1", target: "1", satuan: "Dokumen", labelCode: "T/O 2.1.1" },
              { id: "OUT-2.1.2", oText: "Program Semester", triwulan: "Triwulan 3", target: "1", satuan: "Dokumen", labelCode: "T/O 2.1.2" }
            ]
          }
        },
        {
          id: "RA-3",
          raText: "Menyusun Dokumen Rencana Pelaksanaan Pembelajaran Per Triwulan",
          kriteria: {
            id: "KK-3.1",
            kkText: "Tersusunnya Dokumen Rencana Pelaksanaan Pembelajaran Per Triwulan",
            outputs: [
              { id: "OUT-3.1.1", oText: "Rencana Pelaksanaan Pembelajaran Per Triwulan", triwulan: "Triwulan 1", target: "1", satuan: "Dokumen", labelCode: "T/O 3.1.1" },
              { id: "OUT-3.1.2", oText: "Rencana Pelaksanaan Pembelajaran Per Triwulan", triwulan: "Triwulan 2", target: "1", satuan: "Dokumen", labelCode: "T/O 3.1.2" },
              { id: "OUT-3.1.3", oText: "Rencana Pelaksanaan Pembelajaran Per Triwulan", triwulan: "Triwulan 3", target: "1", satuan: "Dokumen", labelCode: "T/O 3.1.3" },
              { id: "OUT-3.1.4", oText: "Rencana Pelaksanaan Pembelajaran Per Triwulan", triwulan: "Triwulan 4", target: "1", satuan: "Dokumen", labelCode: "T/O 3.1.4" }
            ]
          }
        }
      ]
    },

    // --- RENCANA KINERJA 2 ---
    {
      id: "RHK-2",
      title: "2. Terlaksananya Supervisi Guru dan Tenaga Kependidikan",
      rencanaHasilKerja: "Dokumen pelaksanaan pembelajaran / pembimbingan yang tersusun",
      indikatorKinerja: "Jumlah dokumen pelaksanaan pembelajaran / pembimbingan yang tersusun",
      targetTahunan: "8",
      satuan: "Dokumen",
      rumusRealisasi: "AKUMULASI",
      rumusCapaian: "NORMAL",
      tw1: "2", tw2: "2", tw3: "2", tw4: "2",
      renaksi: [
        {
          id: "RA-4",
          raText: "Menyusun Dokumen Himpunan Bahan Ajar / Media Pembelajaran",
          kriteria: {
            id: "KK-4.1",
            kkText: "Tersusunnya Dokumen Himpunan Bahan Ajar / Media Pembelajaran",
            outputs: [
              { id: "OUT-4.1.1", oText: "Himpunan Bahan Ajar / Media Pembelajaran", triwulan: "Triwulan 1", target: "1", satuan: "Dokumen", labelCode: "T/O 4.1.1" },
              { id: "OUT-4.1.2", oText: "Himpunan Bahan Ajar / Media Pembelajaran", triwulan: "Triwulan 2", target: "1", satuan: "Dokumen", labelCode: "T/O 4.1.2" },
              { id: "OUT-4.1.3", oText: "Himpunan Bahan Ajar / Media Pembelajaran", triwulan: "Triwulan 3", target: "1", satuan: "Dokumen", labelCode: "T/O 4.1.3" },
              { id: "OUT-4.1.4", oText: "Himpunan Bahan Ajar / Media Pembelajaran", triwulan: "Triwulan 4", target: "1", satuan: "Dokumen", labelCode: "T/O 4.1.4" }
            ]
          }
        },
        {
          id: "RA-5",
          raText: "Menyusun Dokumen Daftar Hadir Peserta Didik Bulanan",
          kriteria: {
            id: "KK-5.1",
            kkText: "Tersusunnya Dokumen Daftar Hadir Peserta Didik Bulanan",
            outputs: [
              { id: "OUT-5.1.1", oText: "Daftar Hadir Peserta Didik Bulanan", triwulan: "Triwulan 1", target: "1", satuan: "Dokumen", labelCode: "T/O 5.1.1" },
              { id: "OUT-5.1.2", oText: "Daftar Hadir Peserta Didik Bulanan", triwulan: "Triwulan 2", target: "1", satuan: "Dokumen", labelCode: "T/O 5.1.2" },
              { id: "OUT-5.1.3", oText: "Daftar Hadir Peserta Didik Bulanan", triwulan: "Triwulan 3", target: "1", satuan: "Dokumen", labelCode: "T/O 5.1.3" },
              { id: "OUT-5.1.4", oText: "Daftar Hadir Peserta Didik Bulanan", triwulan: "Triwulan 4", target: "1", satuan: "Dokumen", labelCode: "T/O 5.1.4" }
            ]
          }
        }
      ]
    },

    // --- RENCANA KINERJA 3 ---
    {
      id: "RHK-3",
      title: "3. Terlaksananya Supervisi Guru dan Tenaga Kependidikan",
      rencanaHasilKerja: "Dokumen evaluasi hasil pembelajaran / pembimbingan yang tersusun",
      indikatorKinerja: "Jumlah dokumen evaluasi hasil pembelajaran / pembimbingan yang tersusun",
      targetTahunan: "4",
      satuan: "Dokumen",
      rumusRealisasi: "AKUMULASI",
      rumusCapaian: "NORMAL",
      tw1: "0", tw2: "2", tw3: "0", tw4: "2",
      renaksi: [
        {
          id: "RA-6",
          raText: "Menyusun Laporan Sumatif Akhir Semester",
          kriteria: {
            id: "KK-6.1",
            kkText: "Tersusunnya Laporan Sumatif Akhir Semester",
            outputs: [
              { id: "OUT-6.1.1", oText: "Laporan Sumatif Akhir Semester", triwulan: "Triwulan 2", target: "1", satuan: "Dokumen", labelCode: "T/O 6.1.1",
                subItems: [
                  "Kisi-Kisi Soal Sumatif Akhir Semester",
                  "Soal Sumatif Akhir Semester",
                  "Daftar Nilai Sumatif Akhir Semester",
                  "Analisis Sumatif Akhir Semester",
                  "Dokumen pendukung lainnnya yg masih berkaitan"
                ]
              },
              { id: "OUT-6.1.2", oText: "Laporan Sumatif Akhir Semester", triwulan: "Triwulan 4", target: "1", satuan: "Dokumen", labelCode: "T/O 6.1.2",
                subItems: [
                  "Kisi-Kisi Soal Sumatif Akhir Semester",
                  "Soal Sumatif Akhir Semester",
                  "Daftar Nilai Sumatif Akhir Semester",
                  "Analisis Sumatif Akhir Semester",
                  "Dokumen pendukung lainnnya yg masih berkaitan"
                ]
              }
            ]
          }
        },
        {
          id: "RA-7",
          raText: "Menyusun Laporan Program Tindak Lanjut Hasil Pembelajaran Akhir Semester",
          kriteria: {
            id: "KK-7.1",
            kkText: "Tersusunnya Laporan Program Tindak Lanjut Hasil Pembelajaran Akhir Semester",
            outputs: [
              { id: "OUT-7.1.1", oText: "Laporan Program Tindak Lanjut Hasil Pembelajaran Akhir Semester", triwulan: "Triwulan 2", target: "1", satuan: "Dokumen", labelCode: "T/O 7.1.1",
                subItems: [
                  "Program Remedial",
                  "Program Pengayaan Sumatif Akhir Semester",
                  "Dokumen pendukung lainnnya yg masih berkaitan"
                ]
              },
              { id: "OUT-7.1.2", oText: "Laporan Program Tindak Lanjut Hasil Pembelajaran Akhir Semester", triwulan: "Triwulan 4", target: "1", satuan: "Dokumen", labelCode: "T/O 7.1.2",
                subItems: [
                  "Program Remedial",
                  "Program Pengayaan Sumatif Akhir Semester",
                  "Dokumen pendukung lainnnya yg masih berkaitan"
                ]
              }
            ]
          }
        }
      ]
    },

    // --- RENCANA KINERJA 4 ---
    {
      id: "RHK-4",
      title: "4. Terlaksananya Supervisi Guru dan Tenaga Kependidikan",
      rencanaHasilKerja: "Dokumen pelaksanaan pembimbingan dan pelatihan serta tugas tambahan yang tersusun",
      indikatorKinerja: "Jumlah dokumen pelaksanaan pembimbingan dan pelatihan serta tugas tambahan yang tersusun",
      targetTahunan: "Minimal 4 Dokumen / Maksimal 8 Dokumen",
      satuan: "Dokumen",
      rumusRealisasi: "AKUMULASI",
      rumusCapaian: "NORMAL",
      tw1: "Sesuaikan Berdasarkan Kesepakatan dengan Kepala Sekolah",
      tw2: "Sesuaikan Berdasarkan Kesepakatan dengan Kepala Sekolah",
      tw3: "Sesuaikan Berdasarkan Kesepakatan dengan Kepala Sekolah",
      tw4: "Sesuaikan Berdasarkan Kesepakatan dengan Kepala Sekolah",
      renaksi: [
        {
          id: "RA-8",
          raText: "Menyusun Laporan Pembimbingan dan Pelatihan Kegiatan Kokurikuler",
          kriteria: {
            id: "KK-8.1",
            kkText: "Tersusunnya Laporan Pembimbingan dan Pelatihan Kegiatan Kokurikuler",
            outputs: [
              { id: "OUT-8.1.1", oText: "Laporan Pembimbingan dan Pelatihan Kegiatan Kokurikuler", triwulan: "Triwulan 2", target: "1", satuan: "Dokumen", labelCode: "T/O 8.1.1" },
              { id: "OUT-8.1.2", oText: "Laporan Pembimbingan dan Pelatihan Kegiatan Kokurikuler", triwulan: "Triwulan 4", target: "1", satuan: "Dokumen", labelCode: "T/O 8.1.2" }
            ]
          }
        },
        {
          id: "RA-9",
          raText: "Menyusun Laporan Pembimbingan dan Pelatihan Kegiatan Ekstrakurikuler",
          kriteria: {
            id: "KK-9.1",
            kkText: "Tersusunnya Laporan Pembimbingan dan Pelatihan Kegiatan Ekstrakurikuler",
            outputs: [
              { id: "OUT-9.1.1", oText: "Laporan Pembimbingan dan Pelatihan Kegiatan Ekstrakurikuler", triwulan: "Triwulan 2", target: "1", satuan: "Dokumen", labelCode: "T/O 9.1.1" },
              { id: "OUT-9.1.2", oText: "Laporan Pembimbingan dan Pelatihan Kegiatan Ekstrakurikuler", triwulan: "Triwulan 4", target: "1", satuan: "Dokumen", labelCode: "T/O 9.1.2" }
            ]
          }
        },
        {
          id: "RA-10",
          raText: "Menyusun Laporan Pembimbingan dan Pelatihan Dalam Bentuk Lain",
          kriteria: {
            id: "KK-10.1",
            kkText: "Tersusunnya Laporan Pembimbingan dan Pelatihan Dalam Bentuk Lain",
            outputs: [
              { id: "OUT-10.1.1", oText: "Laporan Pembimbingan dan Pelatihan Dalam Bentuk Lain", triwulan: "Triwulan 2", target: "1", satuan: "Dokumen", labelCode: "T/O 10.1.1" },
              { id: "OUT-10.1.2", oText: "Laporan Pembimbingan dan Pelatihan Dalam Bentuk Lain", triwulan: "Triwulan 4", target: "1", satuan: "Dokumen", labelCode: "T/O 10.1.2" }
            ]
          }
        },
        {
          id: "RA-11",
          raText: "Menyusun Dokumen Pelaksanaan Tugas Tambahan",
          kriteria: {
            id: "KK-11.1",
            kkText: "Tersusunnya Dokumen Pelaksanaan Tugas Tambahan",
            note: "Minimal pilih salah satu, bisa lebih - sesuai pencatatan di Dapodik untuk pemenuhan jam Tunjangan Profesi Guru:",
            target: "2", tw2: "1", tw4: "1",
            pilihan: [
              "Wakil Kepala Satuan Pendidikan",
              "Ketua Program Keahlian Satuan Pendidikan",
              "Kepala Perpustakaan Satuan Pendidikan",
              "Kepala Laboratorium, Bengkel, atau Unit Produksi/TEFA Satuan Pendidikan",
              "Pembimbing Khusus pada Satuan Pendidikan yang Menyelenggarakan Pendidikan Inklusif atau Pendidikan Terpadu",
              "Wali Kelas",
              "Pembina Osis",
              "Koordinator Pengembangan Kompetensi",
              "Pengurus Bursa Kerja Khusus pada SMK",
              "Guru Piket",
              "Pengurus LSP-P1",
              "Koordinator Pengelolaan Kinerja Guru",
              "Koordinator Pembelajaran Berbasis Projek",
              "Koordinator Pembelajaran Pendidikan Inklusia",
              "TPPK/Satgas Perlindungan PTK",
              "Pengurus Kepanitiaan Acara di Satuan Pendidikan",
              "Pengurus Organisasi Bidang Pendidikan",
              "Tutor Pada Pendidikan Kesetaraan",
              "Instruktur/Narsum/Fasilitator pada Program Pengembangan Kompetensi Tingkat Nasional di Bidang Pendidikan",
              "Peserta Program Bangkom yang Terstruktur yang Dilakukan pada Lembaga Penyelenggara Pelatihan/KKG/Komunitas Pendidikan/Organisasi Profesi",
              "Koordinator KKG/MGMP Tingkat Provinsi/Kabupaten/Gugus",
              "Pengurus Organisasi Kemasyarakatan Nonpolitik",
              "Pengurus Organisasi Pemerintah Nonstruktural"
            ]
          }
        }
      ]
    }
  ];

  // --- DATA TAB 5: LAMPIRAN SKP ---
  const LAMPIRAN_DATA = [
    {
      groupTitle: "Dukungan Sumber Daya",
      items: [
        "Dukungan sarana administrasi yang memadai (kertas, stempel, post it, map, dll)",
        "Dukungan pembentukan tim kerja dalam menyelesaikan pekerjaan",
        "Dukungan pimpinan dalam pengambilan kebijakan internal"
      ]
    },
    {
      groupTitle: "Skema Pertanggungjawaban",
      items: [
        "Hasil Kinerja akan dievaluasi setiap 3 bulan sekali sebagai bahan evaluasi pekerjaan berikutnya serta pertanggung jawaban target-target yang telah ditentukan"
      ]
    },
    {
      groupTitle: "Konsekuensi",
      items: [
        "Jika tidak melaporkan hasil kerja, maka akan mempengaruhi penilaian kinerja dalam validasi e-TPP",
        "Jika memberikan kinerja yang baik maka akan diprioritaskan dalam pelatihan dan pengembangan diri"
      ]
    }
  ];

  let currentTabIndex = 0;

  function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 1200);
  }

  function handleCopy(text, element) {
    navigator.clipboard.writeText(text).then(() => {
      showToast('Tersalin: "' + (text.length > 30 ? text.slice(0, 30) + '...' : text) + '"');
      element.classList.add('copied');
      setTimeout(() => element.classList.remove('copied'), 800);
    }).catch(() => {
      const ta = document.createElement('textarea');
      ta.value = text;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      showToast('Tersalin: ' + text);
    });
  }

  function switchTab(index) {
    currentTabIndex = index;

    document.querySelectorAll('.tab-btn').forEach((btn, i) => {
      btn.classList.toggle('active', i === index);
    });

    document.getElementById('searchBox').value = '';

    if (index === 4) {
      document.getElementById('searchBox').style.display = 'none';
      renderLampiran(LAMPIRAN_DATA);
    } else if (index === 5) {
      document.getElementById('searchBox').style.display = 'none';
      renderLainnya(LAINNYA_DATA);
    } else {
      document.getElementById('searchBox').style.display = '';
      renderTree([TREE_DATA[index]]);
    }
  }

  function renderLainnya(rhk) {
    const container = document.getElementById('treeContainer');
    container.innerHTML = '';

    const rootDiv = document.createElement('div');
    rootDiv.className = 'block-item level-root';
    rootDiv.innerHTML = `
      <div class="block-header">
        <span>${rhk.title}</span>
        <span class="btn-action-label" style="color:var(--blue-main)">Isi Form Indikator</span>
      </div>
      <div class="clickable-row" onclick="handleCopy('${rhk.rencanaHasilKerja}', this)">
        <span class="row-label">Rencana Hasil Kerja</span>
        <span class="row-value">${rhk.rencanaHasilKerja}</span>
        <span class="copy-status">✓ Copied</span>
      </div>
      <div class="clickable-row" onclick="handleCopy('${rhk.indikatorKinerja}', this)">
        <span class="row-label">Indikator Kinerja</span>
        <span class="row-value">${rhk.indikatorKinerja}</span>
        <span class="copy-status">✓ Copied</span>
      </div>
      <div class="split-row">
        <div class="clickable-row" onclick="handleCopy('${rhk.targetTahunan}', this)">
          <span class="row-label">Target Tahun</span>
          <span class="row-value">${rhk.targetTahunan}</span>
          <span class="copy-status">✓</span>
        </div>
        <div class="clickable-row" onclick="handleCopy('${rhk.satuan}', this)">
          <span class="row-label">Satuan</span>
          <span class="row-value">${rhk.satuan}</span>
          <span class="copy-status">✓</span>
        </div>
      </div>
      <div class="split-row">
        <div class="clickable-row" onclick="handleCopy('${rhk.rumusRealisasi}', this)">
          <span class="row-label">Realisasi</span>
          <span class="row-value">${rhk.rumusRealisasi}</span>
          <span class="copy-status">✓</span>
        </div>
        <div class="clickable-row" onclick="handleCopy('${rhk.rumusCapaian}', this)">
          <span class="row-label">Capaian</span>
          <span class="row-value">${rhk.rumusCapaian}</span>
          <span class="copy-status">✓</span>
        </div>
      </div>
      <div class="target-grid">
        <div class="clickable-row" onclick="handleCopy('${rhk.tw1.replace(/'/g, "\\'")}', this)"><span class="row-label">TW 1</span><span class="row-value" style="font-size:11px;">${rhk.tw1}</span><span class="copy-status">✓</span></div>
        <div class="clickable-row" onclick="handleCopy('${rhk.tw2.replace(/'/g, "\\'")}', this)"><span class="row-label">TW 2</span><span class="row-value" style="font-size:11px;">${rhk.tw2}</span><span class="copy-status">✓</span></div>
        <div class="clickable-row" onclick="handleCopy('${rhk.tw3.replace(/'/g, "\\'")}', this)"><span class="row-label">TW 3</span><span class="row-value" style="font-size:11px;">${rhk.tw3}</span><span class="copy-status">✓</span></div>
        <div class="clickable-row" onclick="handleCopy('${rhk.tw4.replace(/'/g, "\\'")}', this)"><span class="row-label">TW 4</span><span class="row-value" style="font-size:11px;">${rhk.tw4}</span><span class="copy-status">✓</span></div>
      </div>
    `;
    container.appendChild(rootDiv);

    rhk.renaksi.forEach((ra, raIdx) => {
      const raNode = document.createElement('div');
      raNode.className = 'tree-node';

      const raDiv = document.createElement('div');
      raDiv.className = 'block-item level-ra';
      raDiv.innerHTML = `
        <div class="block-header">
          <span>${ra.id} [Induk Rencana Aksi]</span>
          <span class="btn-action-label" style="color:var(--green-main)">Klik +Tambah Rencana Aksi</span>
        </div>
        <div class="clickable-row" onclick="handleCopy('${ra.raText}', this)">
          <span class="row-label" style="color:var(--green-main)">Nama Renaksi</span>
          <span class="row-value" style="font-weight:600;">${ra.raText}</span>
          <span class="copy-status">✓ Copied</span>
        </div>
      `;
      raNode.appendChild(raDiv);

      const kkNode = document.createElement('div');
      kkNode.className = 'tree-node';
      const kkDiv = document.createElement('div');
      kkDiv.className = 'block-item level-kk';
      kkDiv.innerHTML = `
        <div class="block-header">
          <span>KK [Anak di bawah ${ra.id}]</span>
          <span class="btn-action-label" style="color:var(--amber-main)">Klik +Tambah Kriteria</span>
        </div>
        <div class="clickable-row" onclick="handleCopy('${ra.kriteria.kkText}', this)">
          <span class="row-label" style="color:var(--amber-main)">Kriteria</span>
          <span class="row-value">${ra.kriteria.kkText}</span>
          <span class="copy-status">✓ Copied</span>
        </div>
      `;
      kkNode.appendChild(kkDiv);

      const outNode = document.createElement('div');
      outNode.className = 'tree-node';
      const outDiv = document.createElement('div');
      outDiv.className = 'block-item level-out';

      if (ra.kriteria.pilihan) {
        let chipsHtml = '';
        ra.kriteria.pilihan.forEach((p, i) => {
          const letter = String.fromCharCode(97 + i); // a, b, c...
          const escaped = p.replace(/'/g, "\\'");
          chipsHtml += `<div class="chip-item" onclick="handleCopy('${escaped}', this)">${letter}. ${p}</div>`;
        });
        outDiv.innerHTML = `
          <div class="block-header">
            <span>Output Renaksi [Pilih Salah Satu / Bisa Lebih]</span>
            <span class="btn-action-label" style="color:var(--pink-main)">Klik +Tambah Output</span>
          </div>
          <div class="clickable-row" style="cursor:default; background:#fff7ed;">
            <span class="row-value" style="font-style:italic; color:#9a3412;">${ra.kriteria.note}</span>
          </div>
          <div class="chip-box">${chipsHtml}</div>
          <div class="chip-box" style="margin-top:10px;">
            <div class="chip-item" onclick="handleCopy('${ra.kriteria.target}', this)">🎯 Target Jml: <strong>${ra.kriteria.target}</strong></div>
            <div class="chip-item" onclick="handleCopy('${ra.kriteria.tw2}', this)">📅 TW2: <strong>${ra.kriteria.tw2}</strong></div>
            <div class="chip-item" onclick="handleCopy('${ra.kriteria.tw4}', this)">📅 TW4: <strong>${ra.kriteria.tw4}</strong></div>
          </div>
        `;
      } else {
        const out = ra.kriteria.outputs[0];
        outDiv.innerHTML = `
          <div class="block-header">
            <span>Output Renaksi [Target Output Terakhir]</span>
            <span class="btn-action-label" style="color:var(--pink-main)">Klik +Tambah Output</span>
          </div>
          <div class="clickable-row" onclick="handleCopy('${out.oText.replace(/'/g, "\\'")}', this)">
            <span class="row-label" style="color:var(--pink-main)">Nama Output</span>
            <span class="row-value">${out.oText}</span>
            <span class="copy-status">✓ Copied</span>
          </div>
          <div class="chip-box">
            <div class="chip-item" onclick="handleCopy('${out.target}', this)">🎯 Target Jml: <strong>${out.target}</strong></div>
            <div class="chip-item" onclick="handleCopy('${out.tw2}', this)">📅 TW2: <strong>${out.tw2}</strong></div>
            <div class="chip-item" onclick="handleCopy('${out.tw4}', this)">📅 TW4: <strong>${out.tw4}</strong></div>
          </div>
        `;
      }
      outNode.appendChild(outDiv);
      kkNode.appendChild(outNode);
      raNode.appendChild(kkNode);
      container.appendChild(raNode);
    });
  }

  function renderLampiran(groups) {
    const container = document.getElementById('treeContainer');
    container.innerHTML = '';

    const introDiv = document.createElement('div');
    introDiv.className = 'block-item level-root';
    introDiv.innerHTML = `
      <div class="block-header">
        <span>5. Lampiran SKP: Dukungan Sumber Daya, Skema Pertanggungjawaban & Konsekuensi</span>
        <span class="btn-action-label" style="color:var(--purple-main)">Klik tiap poin untuk menyalin</span>
      </div>
    `;
    container.appendChild(introDiv);

    groups.forEach((group) => {
      const groupNode = document.createElement('div');
      groupNode.className = 'tree-node';

      const groupDiv = document.createElement('div');
      groupDiv.className = 'block-item level-lampiran';

      let itemsHtml = '';
      group.items.forEach((itemText, idx) => {
        const escaped = itemText.replace(/'/g, "\\'");
        itemsHtml += `
          <div class="clickable-row lampiran-row" onclick="handleCopy('${escaped}', this)">
            <span class="lampiran-num">${idx + 1}</span>
            <span class="row-value">${itemText}</span>
            <span class="copy-status">✓ Copied</span>
          </div>
        `;
      });

      groupDiv.innerHTML = `
        <div class="block-header">
          <span>${group.groupTitle}</span>
          <span class="btn-action-label" style="color:var(--purple-main)">${group.items.length} poin</span>
        </div>
        ${itemsHtml}
      `;
      groupNode.appendChild(groupDiv);
      container.appendChild(groupNode);
    });
  }

  function renderTree(items) {
    const container = document.getElementById('treeContainer');
    container.innerHTML = '';

    items.forEach((rhk) => {
      const rootDiv = document.createElement('div');
      rootDiv.className = 'block-item level-root';

      rootDiv.innerHTML = `
        <div class="block-header">
          <span>${rhk.title}</span>
          <span class="btn-action-label" style="color:var(--blue-main)">Langkah 1: Isi Form Indikator</span>
        </div>
        <div class="clickable-row" onclick="handleCopy('${rhk.rencanaHasilKerja}', this)">
          <span class="row-label">Rencana Hasil Kerja</span>
          <span class="row-value">${rhk.rencanaHasilKerja}</span>
          <span class="copy-status">✓ Copied</span>
        </div>
        <div class="clickable-row" onclick="handleCopy('${rhk.indikatorKinerja}', this)">
          <span class="row-label">Indikator Kinerja</span>
          <span class="row-value">${rhk.indikatorKinerja}</span>
          <span class="copy-status">✓ Copied</span>
        </div>
        <div class="split-row">
          <div class="clickable-row" onclick="handleCopy('${rhk.targetTahunan}', this)">
            <span class="row-label">Target Tahun</span>
            <span class="row-value">${rhk.targetTahunan}</span>
            <span class="copy-status">✓</span>
          </div>
          <div class="clickable-row" onclick="handleCopy('${rhk.satuan}', this)">
            <span class="row-label">Satuan</span>
            <span class="row-value">${rhk.satuan}</span>
            <span class="copy-status">✓</span>
          </div>
        </div>
        <div class="split-row">
          <div class="clickable-row" onclick="handleCopy('${rhk.rumusRealisasi}', this)">
            <span class="row-label">Realisasi</span>
            <span class="row-value">${rhk.rumusRealisasi}</span>
            <span class="copy-status">✓</span>
          </div>
          <div class="clickable-row" onclick="handleCopy('${rhk.rumusCapaian}', this)">
            <span class="row-label">Capaian</span>
            <span class="row-value">${rhk.rumusCapaian}</span>
            <span class="copy-status">✓</span>
          </div>
        </div>
        <div class="target-grid">
          <div class="clickable-row" onclick="handleCopy('${rhk.tw1}', this)"><span class="row-label">TW 1</span><span class="row-value">${rhk.tw1}</span><span class="copy-status">✓</span></div>
          <div class="clickable-row" onclick="handleCopy('${rhk.tw2}', this)"><span class="row-label">TW 2</span><span class="row-value">${rhk.tw2}</span><span class="copy-status">✓</span></div>
          <div class="clickable-row" onclick="handleCopy('${rhk.tw3}', this)"><span class="row-label">TW 3</span><span class="row-value">${rhk.tw3}</span><span class="copy-status">✓</span></div>
          <div class="clickable-row" onclick="handleCopy('${rhk.tw4}', this)"><span class="row-label">TW 4</span><span class="row-value">${rhk.tw4}</span><span class="copy-status">✓</span></div>
        </div>
      `;
      container.appendChild(rootDiv);

      if (rhk.renaksi && rhk.renaksi.length > 0) {
        rhk.renaksi.forEach((ra, raIdx) => {
          const raNode = document.createElement('div');
          raNode.className = 'tree-node';

          const raDiv = document.createElement('div');
          raDiv.className = 'block-item level-ra';
          raDiv.innerHTML = `
            <div class="block-header">
              <span>RA ${raIdx + 1} [Induk Rencana Aksi]</span>
              <span class="btn-action-label" style="color:var(--green-main)">Langkah 2: Klik +Tambah Rencana Aksi</span>
            </div>
            <div class="clickable-row" onclick="handleCopy('${ra.raText}', this)">
              <span class="row-label" style="color:var(--green-main)">Nama Renaksi</span>
              <span class="row-value" style="font-weight:600;">${ra.raText}</span>
              <span class="copy-status">✓ Copied</span>
            </div>
          `;
          raNode.appendChild(raDiv);

          if (ra.kriteria) {
            const kkNode = document.createElement('div');
            kkNode.className = 'tree-node';

            const kkDiv = document.createElement('div');
            kkDiv.className = 'block-item level-kk';
            kkDiv.innerHTML = `
              <div class="block-header">
                <span>KK ${raIdx + 1}.1 [Anak di bawah RA ${raIdx + 1}]</span>
                <span class="btn-action-label" style="color:var(--amber-main)">Langkah 3: Klik +Tambah Kriteria</span>
              </div>
              <div class="clickable-row" onclick="handleCopy('${ra.kriteria.kkText}', this)">
                <span class="row-label" style="color:var(--amber-main)">Kriteria</span>
                <span class="row-value">${ra.kriteria.kkText}</span>
                <span class="copy-status">✓ Copied</span>
              </div>
            `;
            kkNode.appendChild(kkDiv);

            if (ra.kriteria.outputs && ra.kriteria.outputs.length > 0) {
              ra.kriteria.outputs.forEach((out) => {
                const outNode = document.createElement('div');
                outNode.className = 'tree-node';

                const outDiv = document.createElement('div');
                outDiv.className = 'block-item level-out';

                if (ra.kriteria.pilihan) {
                   // Render Output Pilihan Tugas Tambahan
                   let chipsHtml = '';
                   ra.kriteria.pilihan.forEach((p, i) => {
                     const letter = String.fromCharCode(97 + i); // a, b, c...
                     const escaped = p.replace(/'/g, "\\'");
                     chipsHtml += `<div class="chip-item" onclick="handleCopy('${escaped}', this)">${letter}. ${p}</div>`;
                   });
                   outDiv.innerHTML = `
                     <div class="block-header">
                       <span>Output Renaksi [Pilih Salah Satu / Bisa Lebih]</span>
                       <span class="btn-action-label" style="color:var(--pink-main)">Klik +Tambah Output</span>
                     </div>
                     <div class="clickable-row" style="cursor:default; background:#fff7ed;">
                       <span class="row-value" style="font-style:italic; color:#9a3412;">${ra.kriteria.note}</span>
                     </div>
                     <div class="chip-box">${chipsHtml}</div>
                     <div class="chip-box" style="margin-top:10px;">
                       <div class="chip-item" onclick="handleCopy('${ra.kriteria.target}', this)">🎯 Target Jml: <strong>${ra.kriteria.target}</strong></div>
                       <div class="chip-item" onclick="handleCopy('${ra.kriteria.tw2}', this)">📅 TW2: <strong>${ra.kriteria.tw2}</strong></div>
                       <div class="chip-item" onclick="handleCopy('${ra.kriteria.tw4}', this)">📅 TW4: <strong>${ra.kriteria.tw4}</strong></div>
                     </div>
                   `;
                } else {
                   // Render Subitems
                   let subItemsHtml = '';
                   let mainCopyText = out.oText;
                   if (out.subItems && out.subItems.length > 0) {
                     const combined = out.oText + ':\n' + out.subItems.map((s, i) => String.fromCharCode(97 + i) + '. ' + s).join('\n');
                     mainCopyText = combined;
                     let chips = '';
                     out.subItems.forEach((s, i) => {
                       const letter = String.fromCharCode(97 + i);
                       const escaped = s.replace(/'/g, "\\'");
                       chips += `<div class="chip-item" onclick="handleCopy('${escaped}', this)">${letter}. ${s}</div>`;
                     });
                     subItemsHtml = `
                       <div class="clickable-row" style="cursor:default; background:#fff7ed;">
                         <span class="row-value" style="font-style:italic; color:#9a3412; font-size:11px;">Rincian dokumen pendukung (klik per poin untuk copy satuan, atau klik "Nama Output" di atas untuk copy semua sekaligus):</span>
                       </div>
                       <div class="chip-box">${chips}</div>
                     `;
                   }
                   const mainCopyEscaped = mainCopyText.replace(/'/g, "\\'").replace(/\n/g, '\\n');

                   outDiv.innerHTML = `
                     <div class="block-header">
                       <span>${out.labelCode} [Target Output Terakhir]</span>
                       <span class="btn-action-label" style="color:var(--pink-main)">Langkah 4: Klik +Tambah Output</span>
                     </div>
                     <div class="clickable-row" onclick="handleCopy('${mainCopyEscaped}', this)">
                       <span class="row-label" style="color:var(--pink-main)">Nama Output${out.subItems ? ' (Klik = Copy Semua)' : ''}</span>
                       <span class="row-value">${out.oText} <strong style="color:var(--pink-main)">[Pilih ${out.triwulan}]</strong></span>
                       <span class="copy-status">✓ Copied</span>
                     </div>
                     ${subItemsHtml}
                     <div class="chip-box">
                       <div class="chip-item" onclick="handleCopy('${out.target}', this)">🎯 Target Jml: <strong>${out.target}</strong></div>
                       <div class="chip-item" onclick="handleCopy('${out.satuan}', this)">📦 Satuan: <strong>${out.satuan}</strong></div>
                       <div class="chip-item" style="background:#fff1f2; border-color:var(--pink-border); cursor:default;">📅 ${out.triwulan}</div>
                     </div>
                   `;
                }

                outNode.appendChild(outDiv);
                kkNode.appendChild(outNode);
              });
            }
            raNode.appendChild(kkNode);
          }
          container.appendChild(raNode);
        });
      }
    });
  }

  document.getElementById('searchBox').addEventListener('input', (e) => {
    if (currentTabIndex === 4 || currentTabIndex === 5) return;
    const value = e.target.value.toLowerCase();
    const currentData = TREE_DATA[currentTabIndex];

    if (currentData.title.toLowerCase().includes(value) ||
        currentData.rencanaHasilKerja.toLowerCase().includes(value) ||
        currentData.indikatorKinerja.toLowerCase().includes(value)) {
        renderTree([currentData]);
    } else {
        const filteredData = JSON.parse(JSON.stringify(currentData));
        filteredData.renaksi = filteredData.renaksi.filter(ra =>
            ra.raText.toLowerCase().includes(value) ||
            (ra.kriteria && ra.kriteria.kkText.toLowerCase().includes(value)) ||
            (ra.kriteria && ra.kriteria.outputs.some(out => out.oText.toLowerCase().includes(value)))
        );

        if (filteredData.renaksi.length > 0) {
            renderTree([filteredData]);
        } else {
            document.getElementById('treeContainer').innerHTML = '<div style="text-align:center; padding:20px; color:#64748b;">Data tidak ditemukan pada halaman ini.</div>';
        }
    }
  });

  switchTab(0);

  // --- ANTI THEFT: DISABLE INSPECT & RIGHT CLICK ---
  document.addEventListener('contextmenu', event => event.preventDefault());

  document.onkeydown = function(e) {
    if (
      e.keyCode === 123 || // F12
      (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) || // Ctrl+Shift+I atau J
      (e.ctrlKey && e.keyCode === 85) // Ctrl+U (View Source)
    ) {
      e.preventDefault();
      return false;
    }
  };
    </script>

</body>

</html>