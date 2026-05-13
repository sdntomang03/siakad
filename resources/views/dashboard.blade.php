<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
            $user = auth()->user();
            $roleName = str_replace('_', ' ', $user->roles->first()->name ?? 'Pengguna');
            @endphp

            <div
                class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 md:p-8 text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl md:text-3xl font-black mb-1">Selamat datang, {{ $user->name }}! 👋</h3>
                    <p class="text-indigo-100">
                        Anda saat ini login sebagai <span
                            class="px-2 py-0.5 bg-white/20 rounded font-bold uppercase tracking-wider text-xs ml-1">{{
                            $roleName }}</span>
                        @if($user->school_id)
                        di <span class="font-bold">{{ $user->school->nama_sekolah ?? 'Sekolah Anda' }}</span>
                        @endif
                    </p>
                </div>
                <div class="hidden md:block p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <svg class="w-12 h-12 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                    </svg>
                </div>
            </div>

            @if($user->hasRole('superadmin'))
            {{-- TAMPILAN KHUSUS SUPERADMIN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-slate-500 dark:text-slate-400 text-sm font-bold uppercase tracking-wider">Total
                        Sekolah</h4>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ \App\Models\School::count() ??
                        0 }}</p>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <div
                        class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-slate-500 dark:text-slate-400 text-sm font-bold uppercase tracking-wider">Total
                        Pengguna</h4>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ \App\Models\User::count() ?? 0
                        }}</p>
                </div>
                <a href="{{ route('superadmin.users.index') }}"
                    class="bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 transition p-6 rounded-2xl shadow-sm flex flex-col items-center justify-center text-white group">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                        </path>
                    </svg>
                    <span class="font-bold">Pengaturan Sistem Pusat</span>
                </a>
            </div>

            @elseif($user->hasRole('operator'))
            {{-- TAMPILAN KHUSUS OPERATOR TU --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('operator.users.index') }}"
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 hover:border-indigo-500 transition group">
                    <div
                        class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center mb-3 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-slate-800 dark:text-white font-bold">Kelola Pengguna</h4>
                    <p class="text-xs text-slate-500 mt-1">Tambah guru, siswa, dan reset password.</p>
                </a>
                <a href="{{ route('classrooms.index') }}"
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 hover:border-amber-500 transition group">
                    <div
                        class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center mb-3 group-hover:bg-amber-500 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-slate-800 dark:text-white font-bold">Kelas & Rombel</h4>
                    <p class="text-xs text-slate-500 mt-1">Atur pembagian kelas siswa.</p>
                </a>
                <a href="{{ route('academic-years.index') }}"
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 hover:border-emerald-500 transition group">
                    <div
                        class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center mb-3 group-hover:bg-emerald-500 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-slate-800 dark:text-white font-bold">Tahun Ajaran</h4>
                    <p class="text-xs text-slate-500 mt-1">Ganti semester dan status aktif.</p>
                </a>
            </div>

            @elseif($user->hasRole('kepsek'))
            {{-- TAMPILAN KHUSUS KEPALA SEKOLAH --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl p-8 text-center shadow-sm border border-slate-200 dark:border-slate-700">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-violet-100 text-violet-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Ringkasan Eksekutif Sekolah</h3>
                <p class="text-slate-500 mt-2 max-w-md mx-auto">Selamat bekerja, Bapak/Ibu Kepala Sekolah. Laporan
                    statistik akademik dan kehadiran hari ini sedang dipersiapkan oleh sistem.</p>
            </div>

            @elseif($user->hasRole('guru'))
            {{-- TAMPILAN KHUSUS GURU --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <h4 class="text-slate-800 dark:text-white font-bold mb-4">Kelas yang Saya Ampu</h4>
                    @php
                    // Cek apakah guru ini menjadi wali kelas di suatu kelas
                    $myClass = \App\Models\Classroom::where('homeroom_teacher_id', $user->employee->id ?? 0)->first();
                    @endphp

                    @if($myClass)
                    <div
                        class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase">Wali
                                Kelas</span>
                            <p class="font-black text-xl text-slate-800 dark:text-slate-200">{{ $myClass->tingkat }} -
                                {{ $myClass->nama_kelas }}</p>
                        </div>
                        <a href="{{ route('classrooms.show', $myClass->id) }}"
                            class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            &rarr;
                        </a>
                    </div>
                    @else
                    <p class="text-sm text-slate-500">Anda belum ditugaskan sebagai wali kelas.</p>
                    @endif
                </div>

                <div class="col-span-1 md:col-span-1 lg:col-span-2 grid grid-cols-2 gap-4">
                    <a href="#"
                        class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 hover:border-emerald-500 transition flex flex-col items-center justify-center text-center">
                        <svg class="w-8 h-8 text-emerald-500 mb-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        <span class="font-bold text-slate-700 dark:text-slate-200">Input Nilai Siswa</span>
                    </a>
                    @if(isset($myClass) && $myClass)
                    <a href="{{ route('attendances.show', $myClass->id) }}"
                        class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 hover:border-blue-500 transition flex flex-col items-center justify-center text-center group">

                        <svg class="w-8 h-8 text-blue-500 mb-2 group-hover:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>

                        <span class="font-bold text-slate-700 dark:text-slate-200">Input Absensi Harian</span>
                    </a>
                    @else
                    <div
                        class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center opacity-70 cursor-not-allowed">
                        <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span class="font-bold text-slate-500">Input Absensi Harian</span>
                        <span class="text-[10px] text-slate-400 mt-1">(Khusus Wali Kelas)</span>
                    </div>
                    @endif
                </div>
            </div>

            @elseif($user->hasRole('siswa'))
            {{-- TAMPILAN KHUSUS SISWA --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="md:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4
                                class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                Informasi Akademik</h4>
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{
                                $user->student->nama_lengkap ?? $user->name }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">NISN: <span class="font-bold">{{
                                    $user->student->nisn ?? 'Belum diisi' }}</span></p>
                        </div>

                        @php
                        // Cek siswa ini masuk kelas mana
                        $myClassroom = $user->student ? $user->student->classrooms()->latest()->first() : null;
                        @endphp

                        <div
                            class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-center">
                            <span class="block text-[10px] font-bold text-emerald-600 uppercase">Kelas Saat Ini</span>
                            <span class="block text-xl font-black text-emerald-700 dark:text-emerald-400">{{
                                $myClassroom->nama_kelas ?? 'Belum Ada' }}</span>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 gap-4">
                        <a href="{{ route('profile.edit') }}"
                            class="py-3 px-4 bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl text-center text-sm font-bold text-slate-700 dark:text-slate-300 transition">
                            Lihat Biodata Lengkap
                        </a>
                        <a href="#"
                            class="py-3 px-4 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 rounded-xl text-center text-sm font-bold text-indigo-700 dark:text-indigo-400 transition">
                            Cek E-Rapor
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>