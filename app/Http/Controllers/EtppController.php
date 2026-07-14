<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EtppController extends Controller
{
    public function index(Request $request)
    {
        $nip = $request->get('nip');
        $employee = null;

        // Jika ada pencarian NIP di URL
        if ($nip) {
            // Cari data pegawai berdasarkan NIP
            $employee = Employee::where('nip', $nip)->first();
        }
        // Jika tidak ada pencarian, dan yang login adalah guru/pegawai, otomatis tampilkan miliknya
        elseif (auth()->user()->hasAnyRole(['guru', 'kepsek', 'operator'])) {
            $employee = auth()->user()->employee;
            $nip = $employee->nip ?? null;
        }

        return view('etpp.index', compact('employee', 'nip'));
    }
}
