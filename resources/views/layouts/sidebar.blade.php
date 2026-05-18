<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
    class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" style="display: none;">
</div>

<aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full md:translate-x-0 md:w-20'"
    class="fixed md:relative inset-y-0 left-0 z-50 flex flex-col h-screen bg-slate-800 dark:bg-slate-950 text-white transition-all duration-300 ease-in-out shadow-2xl md:shadow-xl">

    <div class="h-16 flex items-center px-4 bg-slate-900 transition-all duration-300"
        :class="sidebarOpen ? 'justify-between' : 'justify-center'">

        <div x-show="sidebarOpen" x-transition.opacity.duration.300ms class="flex items-center gap-3 overflow-hidden">
            <div class="p-1.5 bg-indigo-500 rounded-lg shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
            </div>
            <span class="font-bold text-xl tracking-tight truncate">SIAKAD</span>
        </div>

        <button @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 transition-colors focus:outline-none shrink-0"
            title="Toggle Sidebar">

            <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>

            <svg x-show="sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <nav class="flex-1 mt-6 px-4 space-y-2 overflow-y-auto">

        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
            icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            Dashboard
        </x-sidebar-link>

        @role('superadmin')
        <div x-show="sidebarOpen" class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Manajemen Pusat</div>

        <x-sidebar-link :href="route('superadmin.roles.index')" :active="request()->routeIs('superadmin.roles.*')"
            icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
            Manajemen Akses
        </x-sidebar-link>
        <x-sidebar-link :href="route('superadmin.schools.index')"
            :active="request()->routeIs('superadmin.schools.index')"
            icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
            Daftar Sekolah
        </x-sidebar-link>

        <x-sidebar-link :href="route('superadmin.users.index')" :active="request()->routeIs('superadmin.users.index')"
            icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
            Data Pengguna
        </x-sidebar-link>
        @endrole

        @hasanyrole('superadmin|operator|guru|kepsek')
        <div x-show="sidebarOpen" class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Akademik Sekolah
        </div>

        @hasanyrole('superadmin|operator')
        <x-sidebar-link :href="route('academic-years.index')" :active="request()->routeIs('academic-years.*')"
            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
            Tahun Ajaran
        </x-sidebar-link>
        @endhasanyrole

        @role('operator')
        <x-sidebar-link :href="route('operator.users.index')" :active="request()->routeIs('operator.users.*')"
            icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
            Pegawai & Pengguna
        </x-sidebar-link>
        @endrole

        @hasanyrole('superadmin|operator')
        <x-sidebar-link :href="route('subjects.index')" :active="request()->routeIs('subjects.*')"
            icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
            Mata Pelajaran
        </x-sidebar-link>
        @endhasanyrole

        @hasanyrole('superadmin|operator|guru|kepsek')
        <x-sidebar-link :href="route('classrooms.index')" :active="request()->routeIs('classrooms.*')"
            icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
            Kelas & Rombel
        </x-sidebar-link>

        <x-sidebar-link :href="route('attendances.index')" :active="request()->routeIs('attendances.index')"
            icon="M9 17v-2a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
            Absensi
        </x-sidebar-link>
        @endhasanyrole
        @hasanyrole('guru')
        <x-sidebar-link :href="route('assessments.index')" :active="request()->routeIs('assessments.*')"
            icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
            Penilaian
        </x-sidebar-link>
        <x-sidebar-link :href="route('teacher-notes.index')" :active="request()->routeIs('teacher-notes.*')"
            icon="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
            Catatan Siswa
        </x-sidebar-link>


        @endhasanyrole
        @hasanyrole('superadmin|operator|guru|kepsek')
        <x-sidebar-link :href="route('kelulusan.import')" :active="request()->routeIs('kelulusan.import')"
            icon="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
            Pengumuman Kelulusan
        </x-sidebar-link>
        <x-sidebar-link href="#"
            icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
            Buku Induk Siswa
        </x-sidebar-link>
        @endhasanyrole

        @endhasanyrole
    </nav>
</aside>