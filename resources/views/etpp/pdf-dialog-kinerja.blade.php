<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dialog Kinerja Bulanan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1, h2 { text-align: center; margin: 0 0 5px; }
        h1 { font-size: 15px; }
        h2 { font-size: 11px; font-weight: normal; }
        .identity { margin: 18px 0; }
        .identity td { padding: 2px 0; }
        .content { border: 1px solid #374151; padding: 12px; min-height: 250px; line-height: 1.6; white-space: pre-line; }
        .empty { color: #6b7280; font-style: italic; }
    </style>
</head>
<body>
    <h1>DIALOG KINERJA BULANAN</h1>
    <h2>{{ \Carbon\Carbon::createFromDate($validated['tahun'], $validated['bulan'], 1)->locale('id')->isoFormat('MMMM YYYY') }}</h2>

    <table class="identity">
        <tr><td width="100">Nama</td><td>: {{ $employee?->nama_lengkap ?? $user->name }}</td></tr>
        <tr><td>NIP</td><td>: {{ $employee?->nip ?? '-' }}</td></tr>
    </table>

    <div class="content">
        @if($dialogKinerja)
        {{ $dialogKinerja->uraian }}
        @else
        <span class="empty">Belum ada dialog kinerja yang tersimpan untuk periode ini.</span>
        @endif
    </div>
</body>
</html>
