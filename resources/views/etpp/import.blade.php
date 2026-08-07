<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import JSON e-Kinerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #334155;
        }

        input[type="file"] {
            display: block;
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            background-color: #f8fafc;
        }

        .btn-submit {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
        }

        .text-muted {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 style="margin-top: 0; color: #1e293b;">Import Data e-Kinerja</h2>
        <p style="color: #475569; margin-bottom: 25px;">Unggah file berformat <strong>.json</strong> untuk memasukkan
            data Rencana Hasil Kerja (RHK) ke dalam sistem.</p>

        <!-- Menampilkan Pesan Sukses -->
        @if(session('success'))
        <div class="alert alert-success">
            <strong>Berhasil!</strong> {{ session('success') }}
        </div>
        @endif

        <!-- Menampilkan Pesan Error dari Controller (Try-Catch) -->
        @if(session('error'))
        <div class="alert alert-danger">
            <strong>Gagal!</strong> {{ session('error') }}
        </div>
        @endif

        <!-- Menampilkan Error Validasi (Misal: File belum diisi atau format salah) -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form Upload -->
        <form action="{{ route('etpp.import.process') }}" method="POST" enctype="multipart/form-data">
            <!-- Token Keamanan Laravel -->
            @csrf

            <div class="form-group">
                <label for="json_file">Pilih File JSON:</label>
                <input type="file" name="json_file" id="json_file" accept=".json" required>
                <div class="text-muted">Pastikan file memiliki struktur array JSON yang valid.</div>
            </div>

            <button type="submit" class="btn-submit">Mulai Import</button>
        </form>
    </div>

</body>

</html>