<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
    class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" style="display: none;">
</div>

<aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full md:translate-x-0 md:w-20'"
    class="fixed md:relative inset-y-0 left-0 z-50 flex flex-col h-screen min-h-0 bg-slate-800 dark:bg-slate-950 text-slate-300 transition-all duration-300 ease-in-out shadow-2xl md:shadow-xl">

    <div class="h-16 flex items-center px-4 bg-slate-900 transition-all duration-300 text-white"
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

    <nav class="flex-1 mt-4 px-3 space-y-2 overflow-y-auto no-scrollbar pb-6">

        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
            icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            Dashboard
        </x-sidebar-link>

        @role('superadmin')
        <div x-data="{ open: {{ request()->routeIs('superadmin.*') ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="open = !open; if(!sidebarOpen) sidebarOpen = true"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-700 hover:text-white"
                :class="open ? 'bg-slate-800 text-white' : 'text-slate-300'">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">Manajemen Pusat</span>
                </div>
                <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 pr-2 space-y-1">
                <x-sidebar-link :href="route('superadmin.roles.index')"
                    :active="request()->routeIs('superadmin.roles.*')" icon="M9 5l7 7-7 7">
                    Manajemen Akses
                </x-sidebar-link>
                <x-sidebar-link :href="route('superadmin.schools.index')"
                    :active="request()->routeIs('superadmin.schools.index')" icon="M9 5l7 7-7 7">
                    Daftar Sekolah
                </x-sidebar-link>
                <x-sidebar-link :href="route('superadmin.users.index')"
                    :active="request()->routeIs('superadmin.users.index')" icon="M9 5l7 7-7 7">
                    Data Pengguna
                </x-sidebar-link>
            </div>
        </div>
        @endrole

        @hasanyrole('superadmin|operator|guru|kepsek')
        <div x-show="sidebarOpen" class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Akademik & Layanan
        </div>

        @hasanyrole('superadmin|operator')
        <div x-data="{ open: {{ request()->routeIs('academic-years.*', 'operator.users.*', 'subjects.*', 'books.*', 'classrooms.*') ? 'true' : 'false' }} }"
            class="space-y-1">
            <button @click="open = !open; if(!sidebarOpen) sidebarOpen = true"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-700 hover:text-white"
                :class="open ? 'bg-slate-800 text-white' : 'text-slate-300'">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">Data Master</span>
                </div>
                <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 pr-2 space-y-1">
                <x-sidebar-link :href="route('academic-years.index')" :active="request()->routeIs('academic-years.*')"
                    icon="M9 5l7 7-7 7">
                    Tahun Ajaran
                </x-sidebar-link>
                @role('operator')
                <x-sidebar-link :href="route('operator.users.index')" :active="request()->routeIs('operator.users.*')"
                    icon="M9 5l7 7-7 7">
                    Pegawai & Pengguna
                </x-sidebar-link>
                @endrole
                <x-sidebar-link :href="route('subjects.index')" :active="request()->routeIs('subjects.*')"
                    icon="M9 5l7 7-7 7">
                    Mata Pelajaran
                </x-sidebar-link>
                <x-sidebar-link :href="route('classrooms.index')" :active="request()->routeIs('classrooms.*')"
                    icon="M9 5l7 7-7 7">
                    Kelas & Rombel
                </x-sidebar-link>
                <x-sidebar-link :href="route('books.index')" :active="request()->routeIs('books.*')"
                    icon="M9 5l7 7-7 7">
                    Daftar Buku
                </x-sidebar-link>
            </div>
        </div>
        @endhasanyrole

        <div x-data="{ open: {{ request()->routeIs('attendances.*', 'assessments.*', 'teacher-notes.*', 'piket.*', 'jadwal.*') ? 'true' : 'false' }} }"
            class="space-y-1">
            <button @click="open = !open; if(!sidebarOpen) sidebarOpen = true"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-700 hover:text-white"
                :class="open ? 'bg-slate-800 text-white' : 'text-slate-300'">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">KBM & Kesiswaan</span>
                </div>
                <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 pr-2 space-y-1">
                @hasanyrole('superadmin|operator|guru|kepsek')
                <x-sidebar-link :href="route('attendances.index')" :active="request()->routeIs('attendances.index')"
                    icon="M9 5l7 7-7 7">
                    Absensi
                </x-sidebar-link>
                @endhasanyrole

                @role('guru')
                <x-sidebar-link :href="route('assessments.index')" :active="request()->routeIs('assessments.*')"
                    icon="M9 5l7 7-7 7">
                    Penilaian
                </x-sidebar-link>
                <x-sidebar-link :href="route('teacher-notes.index')" :active="request()->routeIs('teacher-notes.*')"
                    icon="M9 5l7 7-7 7">
                    Catatan Siswa
                </x-sidebar-link>

                <!-- Menu Baru: Jadwal Pelajaran -->
                <x-sidebar-link :href="route('jadwal.index')" :active="request()->routeIs('jadwal.*')"
                    icon="M9 5l7 7-7 7">
                    Jadwal Pelajaran
                </x-sidebar-link>


                <x-sidebar-link :href="route('piket.jurnal')" :active="request()->routeIs('piket.jurnal')"
                    icon="M9 5l7 7-7 7">
                    Jurnal Piket Harian
                </x-sidebar-link>

                @endrole
            </div>
        </div>

        <div x-data="{ open: {{ request()->routeIs('book-loans.*', 'report-submissions.*', 'admin.asset-tracking.*', 'kelulusan.*') ? 'true' : 'false' }} }"
            class="space-y-1">
            <button @click="open = !open; if(!sidebarOpen) sidebarOpen = true"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-slate-700 hover:text-white"
                :class="open ? 'bg-slate-800 text-white' : 'text-slate-300'">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">Administrasi</span>
                </div>
                <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 pr-2 space-y-1">
                <x-sidebar-link :href="route('book-loans.index')" :active="request()->routeIs('book-loans.*')"
                    icon="M9 5l7 7-7 7">
                    Peminjaman Buku
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.asset-tracking.index')"
                    :active="request()->routeIs('admin.asset-tracking.*', 'admin.assets.*')" icon="M9 5l7 7-7 7">
                    Manajemen Aset
                </x-sidebar-link>
                <x-sidebar-link :href="route('report-submissions.index')"
                    :active="request()->routeIs('report-submissions.*')" icon="M9 5l7 7-7 7">
                    Pengembalian Rapor
                </x-sidebar-link>

                @hasanyrole('superadmin|operator|kepsek')
                <x-sidebar-link :href="route('kelulusan.import')" :active="request()->routeIs('kelulusan.*')"
                    icon="M9 5l7 7-7 7">
                    Data Kelulusan
                </x-sidebar-link>
                @endhasanyrole

                <x-sidebar-link href="#" icon="M9 5l7 7-7 7">
                    Buku Induk Siswa
                </x-sidebar-link>
            </div>
        </div>
        @endhasanyrole
        <x-sidebar-link :href="route('modul.generator')" :active="request()->routeIs('modul.*')"
            icon="M11.017 2.814a1 1 0 011.966 0l1.051 5.558a2 2 0 001.594 1.594l5.558 1.051a1 1 0 010 1.966l-5.558 1.051a2 2 0 00-1.594 1.594l-1.051 5.558a1 1 0 01-1.966 0l-1.051-5.558a2 2 0 00-1.594-1.594l-5.558-1.051a1 1 0 010-1.966l5.558-1.051a2 2 0 001.594-1.594l1.051-5.558zM20 2v4m2-2h-4">
            Modul Ajar AI
        </x-sidebar-link>
    </nav>
</aside>