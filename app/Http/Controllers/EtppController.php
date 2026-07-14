<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EtppController extends Controller
{
    /**
     * Menangkap inputan dari Form, lalu mengubahnya menjadi URL bersih
     */
    public function search(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
        ]);

        // Mengalihkan pengguna ke: /etpp/198502022010012004
        return redirect()->route('etpp.show', ['nip' => $request->nip]);
    }

    /**
     * Memproses NIP yang ada di dalam URL
     */
    public function show($nip = null)
    {
        $user = auth()->user();

        // Skenario 1: Jika URL hanya dipanggil "/etpp" (tanpa NIP di belakangnya)
        if (! $nip) {
            // Jika yang login adalah guru/pegawai yang punya NIP, otomatis redirect ke NIP dia sendiri
            if ($user->hasAnyRole(['guru', 'kepsek', 'operator']) && isset($user->employee->nip)) {
                return redirect()->route('etpp.show', ['nip' => $user->employee->nip]);
            }

            // Jika yang login admin/tidak punya NIP, tampilkan form kosong
            return view('etpp.show', ['employee' => null, 'nip' => null]);
        }

        // Skenario 2: Jika ada NIP di URL (Misal: /etpp/198502022010012004)
        // Cari data pegawai berdasarkan NIP tersebut
        $employee = Employee::where('nip', $nip)->first();

        // Lempar datanya ke tampilan HTML
        return view('etpp.show', compact('employee', 'nip'));
    }
}
