<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dialog Kinerja Bulanan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1, h2 { text-align: center; margin: 0 0 5px; }
        h1 { font-size: 15px; } h2 { font-size: 11px; font-weight: normal; }
        table { border-collapse: collapse; width: 100%; margin-top: 16px; }
        th, td { border: 1px solid #374151; padding: 6px; vertical-align: top; }
        th { background: #e5e7eb; text-align: center; }
        .footer { margin-top: 16px; }
    </style>
</head>
<body>
    <h1>DIALOG KINERJA</h1>
    <h2>{{ $employee?->nama_lengkap ?? $user->name }}</h2>
    <h2>NIP: {{ $employee?->nip ?? '-' }}</h2>
    <h2>{{ $school?->nama_sekolah ?? config('app.name') }}</h2>
    <h2>{{ \Carbon\Carbon::createFromDate($validated['tahun'], $validated['bulan'], 1)->locale('id')->isoFormat('MMMM YYYY') }}</h2>
    <table>
        <thead><tr><th width="8%">No.</th><th>Dialog Kinerja</th></tr></thead>
        <tbody>
            @forelse($dialogKinerja?->items ?? [] as $index => $item)
            <tr>
                <td style="text-align: center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->outputTarget?->deskripsi_output ?? $item->nama_output }}</strong><br>
                    <span>Link: </span>
                    @if($item->link_referensi)
                    <a href="{{ $item->link_referensi }}">{{ $item->link_referensi }}</a>
                    @else
                    -
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="2" style="text-align: center">Belum ada link dialog kinerja untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="footer">Rekap publik: <a href="{{ $recapUrl }}">{{ $recapUrl }}</a></p>
</body>
</html>
