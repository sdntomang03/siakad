<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                    Monitoring Peminjaman Siswa
                </h2>
                <p class="text-sm text-slate-500 mt-1">Klik pada nama siswa untuk melihat dan mengembalikan buku yang
                    dipinjam.</p>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">

                {{-- TOMBOL EXCEL BARU --}}
                <a href="{{ route('book-loans.export-unreturned') }}"
                    class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-emerald-700 transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Export Excel
                </a>

                <a href="{{ route('book-loans.index') }}"
                    class="w-full sm:w-auto px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold text-center hover:bg-slate-300 transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ALERTS --}}
            @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 mb-4">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            {{-- FITUR PENCARIAN SISWA LOKAL --}}
            <div
                class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <input type="text" id="searchInput" placeholder="Cari nama siswa..."
                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 text-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">
            </div>

            {{-- DAFTAR SISWA (ACCORDION) --}}
            <div class="space-y-3" id="studentList">
                @forelse($students as $student)
                @php
                $activeLoansCount = $student->bookLoans->count();
                $kelas = $student->kelasAktif();
                @endphp

                {{-- Hanya tampilkan siswa yang punya pinjaman agar lebih rapi, tapi bisa diatur jika ingin semua --}}
                <div x-data="{ open: false }"
                    class="student-card bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden"
                    data-name="{{ strtolower($student->nama_lengkap) }}">

                    {{-- HEADER ACCORDION --}}
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-5 focus:outline-none hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4 text-left">
                            <div
                                class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-lg">
                                {{ substr($student->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm md:text-base">{{
                                    $student->nama_lengkap }}</h3>
                                <p class="text-xs text-slate-500">{{ $kelas ? 'Kelas '.$kelas->tingkat.'
                                    '.$kelas->nama_kelas : 'Belum masuk kelas' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            @if($activeLoansCount > 0)
                            <span
                                class="px-3 py-1 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-xs font-bold rounded-full border border-rose-200 dark:border-rose-800">
                                {{ $activeLoansCount }} Dipinjam
                            </span>
                            @else
                            <span
                                class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-full border border-emerald-200 dark:border-emerald-800 hidden sm:inline-block">
                                Tidak ada tanggungan
                            </span>
                            @endif
                            <svg :class="open ? 'rotate-180 text-indigo-600' : 'text-slate-400'"
                                class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>

                    {{-- DETAIL BUKU DIPINJAM --}}
                    <div x-show="open" x-collapse
                        class="border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50"
                        style="display: none;">
                        <div class="p-5">
                            @if($activeLoansCount > 0)
                            <div
                                class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                                    <thead
                                        class="text-xs text-slate-700 uppercase bg-slate-100 dark:bg-slate-900/80 dark:text-slate-300">
                                        <tr>
                                            <th class="px-4 py-3">Judul Buku</th>
                                            <th class="px-4 py-3 whitespace-nowrap">Tgl Pinjam</th>
                                            <th class="px-4 py-3 whitespace-nowrap">Jatuh Tempo</th>
                                            <th class="px-4 py-3 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        @foreach($student->bookLoans as $loan)

                                        {{-- Inisialisasi Alpine.js state: isReturned dan isLoading --}}
                                        <tr x-data="{ isReturned: false, isLoading: false }"
                                            class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">

                                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">
                                                {{-- Coret nama buku jika sudah dikembalikan --}}
                                                <span
                                                    :class="isReturned ? 'line-through text-slate-400 dark:text-slate-500' : ''">
                                                    {{ $loan->book_title }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs">{{
                                                \Carbon\Carbon::parse($loan->borrowed_at)->format('d M Y') }}</td>
                                            <td class="px-4 py-3 text-xs">
                                                @if($loan->due_at)
                                                @php $isLate = \Carbon\Carbon::parse($loan->due_at)->isPast(); @endphp
                                                <span class="{{ $isLate ? 'text-rose-600 font-bold' : '' }}">
                                                    {{ \Carbon\Carbon::parse($loan->due_at)->format('d M Y') }}
                                                </span>
                                                @else
                                                —
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">

                                                {{-- TOMBOL TERIMA BUKU (Hanya Muncul Jika Belum Dikembalikan) --}}
                                                <button x-show="!isReturned" @click="if(confirm('Tandai buku ini sudah dikembalikan?')) {
                        isLoading = true;
                        fetch('{{ route('book-loans.return', $loan->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) { isReturned = true; }
                            isLoading = false;
                        })
                        .catch(() => { isLoading = false; alert('Terjadi kesalahan jaringan.'); });
                    }" :disabled="isLoading" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider hover:bg-emerald-700 shadow-sm transition disabled:opacity-50">
                                                    <span x-show="!isLoading">Terima Buku</span>
                                                    <span x-show="isLoading">Memproses...</span>
                                                </button>

                                                {{-- LABEL DIKEMBALIKAN (Hanya Muncul Jika Proses Berhasil) --}}
                                                <span x-show="isReturned" style="display: none;"
                                                    class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                                    Dikembalikan
                                                </span>

                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-6 text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                                <p>Siswa ini tidak memiliki pinjaman buku aktif.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div
                    class="text-center py-10 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-500">
                    <p>Tidak ada data siswa ditemukan.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- SCRIPT PENCARIAN SISWA --}}
    <script>
        document.getElementById('searchInput').addEventListener('input', function(e) {
            let searchTerm = e.target.value.toLowerCase();
            let cards = document.querySelectorAll('.student-card');

            cards.forEach(card => {
                let name = card.getAttribute('data-name');
                if(name.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>