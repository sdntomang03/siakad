<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import JSON e-Kinerja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-900 dark:text-slate-100">

    <main class="px-4 py-6 sm:px-6 sm:py-10">
        <section class="mx-auto max-w-2xl rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800 sm:p-8">
        <h1 class="text-2xl font-semibold text-slate-950 dark:text-white">Import Data e-Kinerja</h1>
        <p class="mb-6 mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Unggah file berformat <strong>.json</strong> untuk memasukkan
            data Rencana Hasil Kerja (RHK) ke dalam sistem.</p>

        @if(session('success'))
        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-200" role="status">
            <strong>Berhasil!</strong> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 rounded-md border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-800 dark:bg-rose-950/30 dark:text-rose-200" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 rounded-md border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-800 dark:bg-rose-950/30 dark:text-rose-200" role="alert">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('etpp.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="json_file" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Pilih File JSON</label>
                <input type="file" name="json_file" id="json_file" accept=".json" required
                    class="block min-h-11 w-full rounded-md border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:min-h-11 file:border-0 file:bg-slate-100 file:px-4 file:font-medium file:text-slate-700 hover:file:bg-slate-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:file:bg-slate-700 dark:file:text-slate-100">
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Pastikan file memiliki struktur array JSON yang valid.</p>
            </div>

            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">Mulai Import</button>
        </form>
        </section>
    </main>

</body>

</html>