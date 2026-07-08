<?php

namespace App\Http\Controllers;

class RenkinController extends Controller
{
    /**
     * Menampilkan halaman Rencana Kinerja Guru.
     */
    public function index()
    {
        // Memanggil file resources/views/renkin.blade.php
        return view('renkin');
    }
}
