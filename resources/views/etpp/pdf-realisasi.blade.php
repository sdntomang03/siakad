<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Realisasi e-TPP</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1, h2 { text-align: center; margin: 0 0 5px; }
        h1 { font-size: 15px; }
        h2 { font-size: 11px; font-weight: normal; }
        .identity { margin: 18px 0; }
        .identity td { padding: 2px 0; }
        table.report { border-collapse: collapse; width: 100%; }
        table.report th, table.report td { border: 1px solid #374151; padding: 6px; vertical-align: top; }
        table.report th { background: #e5e7eb; text-align: center; }
        .empty { text-align: center; padding: 18px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>REALISASI E-TPP</h1>
    <h2>{{ $validated['triwulan'] }} Tahun {{ $validated['tahun'] }}</h2>

    <table class="identity">
        <tr><td width="100">Nama</td><td>: {{ $employee?->nama_lengkap ?? $user->name }}</td></tr>
        <tr><td>NIP</td><td>: {{ $employee?->nip ?? '-' }}</td></tr>
    </table>

    <table class="report">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="20%">Kategori</th>
                <th width="25%">Rencana Aksi / Output</th>
                <th>Realisasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($realisasiList as $index => $realisasi)
            <tr>
                <td style="text-align: center">{{ $index + 1 }}</td>
                <td>{{ $realisasi->outputTarget->rencanaAksi->rhk->kategori->nama_kategori ?? '-' }}</td>
                <td>
                    <strong>Rencana Aksi:</strong> {{ $realisasi->outputTarget->rencanaAksi->deskripsi_ra ?? '-' }}<br>
                    <strong>Output:</strong> {{ $realisasi->outputTarget->deskripsi_output }}
                </td>
                <td>{{ $realisasi->realisasi }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty">Belum ada realisasi yang tersimpan untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
