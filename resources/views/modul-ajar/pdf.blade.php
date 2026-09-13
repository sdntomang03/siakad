<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Modul Ajar</title>
    <style>
        /* CSS Murni untuk menerjemahkan class Tailwind agar dikenali DomPDF */
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
            /* BARU: Kunci tata letak tabel secara kaku */
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        td {
            padding: 6px;
            vertical-align: top;
            /* Tambahkan word-wrap agar teks panjang tidak menjebol tabel */
            word-wrap: break-word;
        }

        /* BARU: Paksa ukuran rasio kolom (30% Kiri : 70% Kanan) secara global */
        td[colspan="2"] {
            width: 25%;
        }

        td[colspan="4"] {
            width: 75%;
        }

        td[colspan="6"] {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-2xl {
            font-size: 18pt;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .underline {
            text-decoration: underline;
        }

        /* Pewarnaan Tabel */
        .bg-blue-600 {
            background-color: #2563eb;
            color: #ffffff;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .text-blue-800 {
            color: #1e40af;
        }

        .bg-emerald-600 {
            background-color: #059669;
            color: #ffffff;
        }

        .bg-emerald-100 {
            background-color: #d1fae5;
        }

        .bg-emerald-50 {
            background-color: #ecfdf5;
        }

        .text-emerald-900 {
            color: #064e3b;
        }

        .bg-amber-500 {
            background-color: #f59e0b;
            color: #ffffff;
        }

        .bg-amber-200 {
            background-color: #fde68a;
        }

        .bg-amber-50 {
            background-color: #fffbeb;
        }

        .text-amber-900 {
            color: #78350f;
        }

        /* Tata letak Tanda Tangan */
        .w-1\/2 {
            width: 50%;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mb-10 {
            margin-bottom: 2.5rem;
        }

        .mb-20 {
            margin-bottom: 5rem;
        }

        /* List */
        ul,
        ol {
            margin-top: 2px;
            margin-bottom: 2px;
            padding-left: 30px;
        }

        li {
            margin-bottom: 3px;
        }

        /* Mencegah tabel terpotong di tengah baris */
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <!-- Cetak HTML dari Database langsung ke body -->
    {!! $modul->html_content !!}
</body>

</html>