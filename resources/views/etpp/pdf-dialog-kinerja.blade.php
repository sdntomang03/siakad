```php
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dialog Kinerja Bulanan</title>

    <style>
        /*
        |--------------------------------------------------------------------------
        | KONFIGURASI DOKUMEN
        |--------------------------------------------------------------------------
        | Dirancang untuk PDF / Dompdf
        */

        @page {
            margin: 25px 30px 28px 30px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;

            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.5;

            color: #1f2937;
            background: #ffffff;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER DOKUMEN
        |--------------------------------------------------------------------------
        */

        .document-header {
            width: 100%;

            text-align: center;

            margin-bottom: 18px;
            padding-bottom: 12px;

            border-bottom: 2px solid #1f2937;
        }

        .document-title {
            margin: 0;

            font-size: 15px;
            line-height: 1.4;

            font-weight: bold;

            text-transform: uppercase;

            color: #111827;

            letter-spacing: 0.2px;
        }

        .document-period {
            margin: 4px 0 0;

            font-size: 11px;
            line-height: 1.4;

            font-weight: normal;

            color: #4b5563;
        }


        /*
        |--------------------------------------------------------------------------
        | IDENTITAS PEGAWAI
        |--------------------------------------------------------------------------
        */

        .identity-wrapper {
            margin-bottom: 18px;
        }

        .identity-table {
            width: 100%;

            margin: 0;

            border-collapse: collapse;

            border: none;
        }

        .identity-table td {
            border: none;

            padding: 2px 3px;

            vertical-align: top;

            line-height: 1.5;
        }

        .identity-label {
            width: 70px;

            font-weight: bold;

            color: #374151;

            white-space: nowrap;
        }

        .identity-colon {
            width: 12px;

            text-align: center;

            font-weight: bold;

            color: #374151;
        }

        .identity-value {
            color: #111827;

            font-weight: normal;
        }


        /*
        |--------------------------------------------------------------------------
        | GARIS PEMBATAS
        |--------------------------------------------------------------------------
        */

        .identity-divider {
            margin-top: 9px;

            height: 1px;

            background-color: #d1d5db;
        }


        /*
        |--------------------------------------------------------------------------
        | SECTION TITLE
        |--------------------------------------------------------------------------
        */

        .section-header {
            margin: 0 0 8px;

            font-size: 11px;

            font-weight: bold;

            text-transform: uppercase;

            color: #111827;
        }


        /*
        |--------------------------------------------------------------------------
        | TABEL DIALOG KINERJA
        |--------------------------------------------------------------------------
        */

        .data-table {
            width: 100%;

            margin: 0;

            border-collapse: collapse;

            table-layout: fixed;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #6b7280;

            padding: 7px 8px;

            vertical-align: top;
        }

        .data-table th {
            background-color: #e5e7eb;

            color: #111827;

            font-size: 9.5px;

            font-weight: bold;

            text-align: center;

            vertical-align: middle;

            text-transform: uppercase;
        }

        .data-table td {
            background-color: #ffffff;

            font-size: 9.5px;
        }


        /*
        |--------------------------------------------------------------------------
        | KOLOM
        |--------------------------------------------------------------------------
        */

        .column-number {
            width: 8%;
        }

        .column-dialog {
            width: 92%;
        }


        /*
        |--------------------------------------------------------------------------
        | NOMOR
        |--------------------------------------------------------------------------
        */

        .number-cell {
            text-align: center;

            vertical-align: middle !important;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | OUTPUT / DIALOG
        |--------------------------------------------------------------------------
        */

        .dialog-title {
            margin: 0 0 5px;

            font-weight: bold;

            color: #111827;
        }


        /*
        |--------------------------------------------------------------------------
        | LINK
        |--------------------------------------------------------------------------
        */

        .link-label {
            display: inline-block;

            margin-right: 4px;

            font-weight: bold;

            color: #374151;
        }

        .reference-link {
            color: #1d4ed8;

            text-decoration: underline;

            word-break: break-all;

            overflow-wrap: anywhere;
        }

        .no-link {
            color: #6b7280;

            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .empty-state {
            padding: 14px !important;

            text-align: center;

            color: #6b7280;

            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER / REKAP PUBLIK
        |--------------------------------------------------------------------------
        */

        .document-footer {
            margin-top: 18px;

            padding-top: 9px;

            border-top: 1px solid #d1d5db;

            font-size: 8.5px;

            color: #6b7280;
        }

        .footer-label {
            font-weight: bold;

            color: #374151;
        }

        .footer-link {
            color: #374151;

            word-break: break-all;

            overflow-wrap: anywhere;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF / PRINT
        |--------------------------------------------------------------------------
        */

        tr {
            page-break-inside: avoid;
        }

        .identity-wrapper,
        .section-header,
        .document-footer {
            page-break-inside: avoid;
        }

        a {
            text-decoration: underline;
        }
    </style>
</head>


<body>


    <!-- ============================================================
         HEADER
    ============================================================= -->

    <div class="document-header">

        <h1 class="document-title">
            KEMAJUAN TARGET RENCANA AKSI<br>
            MELALUI PELAPORAN DIALOG KINERJA - GURU KELAS SD
        </h1>

        <div class="document-period">
            Periode {{ $periode }} - {{ $namaBulan }} {{ $validated['tahun'] }} - {{ str_replace('TW ', 'Triwulan ', $triwulan) }}
        </div>

    </div>


    <!-- ============================================================
         IDENTITAS PEGAWAI
    ============================================================= -->

    <div class="identity-wrapper">

        <table class="identity-table">

            <tr>
                <td class="identity-label">
                    NAMA
                </td>

                <td class="identity-colon">
                    :
                </td>

                <td class="identity-value">
                    {{ $employee?->nama_lengkap ?? $user->name }}
                </td>
            </tr>


            <tr>
                <td class="identity-label">
                    NIP/NRK
                </td>

                <td class="identity-colon">
                    :
                </td>

                <td class="identity-value">
                    {{ $employee?->nip ?? '-' }}/{{ $employee?->nrk ?? '-' }}
                </td>
            </tr>


            <tr>
                <td class="identity-label">
                    SEKOLAH
                </td>

                <td class="identity-colon">
                    :
                </td>

                <td class="identity-value">
                    {{ $school?->nama_sekolah ?? config('app.name') }}
                </td>
            </tr>

        </table>


        <div class="identity-divider"></div>

    </div>


    <!-- ============================================================
         SECTION
    ============================================================= -->

    <div class="section-header">
        Daftar Dialog Kinerja
    </div>


    <!-- ============================================================
         TABEL DIALOG KINERJA
    ============================================================= -->

    <table class="data-table">

        <thead>

            <tr>

                <th class="column-number">
                    No.
                </th>

                <th class="column-dialog">
                    Dialog Kinerja
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($dialogKinerja?->items ?? [] as $index => $item)

            <tr>

                <!-- NOMOR -->
                <td class="number-cell">
                    {{ $index + 1 }}
                </td>


                <!-- DIALOG KINERJA -->
                <td>

                    <div class="dialog-title">

                        {{ $item->outputTarget?->deskripsi_output
                        ?? $item->nama_output
                        ?? '-' }}

                    </div>


                    <!-- LINK REFERENSI -->
                    <div>

                        <span class="link-label">
                            Link:
                        </span>

                        @if($item->link_referensi)

                        <a class="reference-link" href="{{ $item->link_referensi }}">
                            {{ $item->link_referensi }}
                        </a>

                        @else

                        <span class="no-link">
                            Belum tersedia
                        </span>

                        @endif

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="2" class="empty-state">
                    Belum ada link dialog kinerja untuk periode ini.
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>


    <!-- ============================================================
         FOOTER
    ============================================================= -->

    <div class="document-footer">

        <span class="footer-label">
            Rekap publik:
        </span>

        <a class="footer-link" href="{{ $recapUrl }}">
            {{ $recapUrl }}
        </a>

    </div>


</body>

</html>