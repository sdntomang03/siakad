<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200">
                Peminjaman Buku
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ALERTS --}}
            @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
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

            @if(session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-bold text-amber-800">{{ session('warning') }}</p>
                </div>
            </div>
            @endif

            {{-- CARD 1: FORM PEMINJAMAN --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300">Form Peminjaman Buku</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catat peminjaman buku baru untuk siswa.</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('book-loans.store') }}" method="POST" class="grid lg:grid-cols-12 gap-6">
                        @csrf

                        {{-- Kolom Kiri: Siswa --}}
                        <div
                            class="lg:col-span-5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-5">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih
                                Siswa</label>
                            <div class="flex items-center gap-2 mb-3">
                                <input type="checkbox" id="select_all_students"
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer">
                                <label for="select_all_students"
                                    class="text-xs font-bold text-slate-500 cursor-pointer hover:text-slate-700 uppercase">Pilih
                                    semua siswa di daftar</label>
                            </div>
                            <select id="student_select" name="student_ids[]" multiple size="7"
                                class="block w-full rounded-xl border border-slate-300 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @foreach($students as $s)
                                @php $kelas = $s->kelasAktif(); @endphp
                                <option value="{{ $s->id }}" data-tingkat="{{ $kelas->tingkat ?? '' }}"
                                    class="py-1.5 px-3 hover:bg-indigo-50 dark:hover:bg-slate-700 rounded-md font-medium">
                                    {{ $s->nama_lengkap }}{{ $kelas ? ' — Kelas '.$kelas->tingkat.' '.$kelas->nama_kelas
                                    : '' }}
                                </option>
                                @endforeach
                            </select>
                            <div class="text-[11px] text-slate-400 mt-3 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Tekan Ctrl/Cmd sambil klik untuk memilih beberapa siswa.
                            </div>
                            @error('student_ids')<p class="mt-2 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kolom Kanan: Detail Buku --}}
                        <div class="lg:col-span-7 grid gap-5 content-start">
                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Filter
                                        Tingkat</label>
                                    <select id="tingkat_select"
                                        class="block w-full rounded-xl border border-slate-300 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Semua Tingkat</option>
                                        @for($i = 1; $i <= 6; $i++) <option value="{{ $i }}">Kelas {{ $i }}</option>
                                            @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih
                                        Buku Paket</label>
                                    <select id="book_select" name="book_id"
                                        class="block w-full rounded-xl border border-slate-300 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">-- Pilih Buku --</option>
                                        @php
                                        $booksByTingkat = \App\Models\Book::where('school_id',
                                        auth()->user()->school_id)
                                        ->where('type', 'paket')->orderBy('tingkat')->orderBy('title')->get();
                                        @endphp
                                        @foreach($booksByTingkat as $b)
                                        <option value="{{ $b->id }}" data-tingkat="{{ $b->tingkat }}">
                                            {{ $b->tingkat ? 'Kelas '.$b->tingkat.' — ' : '' }}{{ $b->title }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Atau
                                    Tulis Judul Manual <span
                                        class="text-slate-400 font-normal">(opsional)</span></label>
                                <input type="text" name="book_title" value="{{ old('book_title') }}"
                                    placeholder="Ketik judul buku di sini..."
                                    class="block w-full rounded-xl border border-slate-300 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                @error('book_title')<p class="mt-2 text-xs text-red-500 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal
                                    Pengembalian <span class="text-slate-400 font-normal">(opsional)</span></label>
                                <input type="datetime-local" name="returned_at" value="{{ old('returned_at') }}"
                                    class="block w-full rounded-xl border border-slate-300 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                <p class="mt-2 text-[11px] text-slate-400">Isi hanya jika ingin langsung menandai buku
                                    sudah dikembalikan.</p>
                                @error('returned_at')<p class="mt-2 text-xs text-red-500 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit"
                                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 transition flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Proses Peminjaman
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- CARD 2: RIWAYAT PEMINJAMAN DENGAN DATATABLES --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300">Riwayat Peminjaman & Pengembalian
                    </h3>
                </div>

                <div
                    class="p-6 text-xs text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700">
                    Tabel ini dilengkapi pencarian, penyortiran, dan pagination klien untuk mempercepat pencarian
                    riwayat peminjaman.
                </div>

                {{-- CSS Khusus untuk Merapikan DataTables agar selaras dengan Tailwind --}}
                <style>
                    .dataTables_wrapper .dataTables_length,
                    .dataTables_wrapper .dataTables_filter,
                    .dataTables_wrapper .dataTables_info,
                    .dataTables_wrapper .dataTables_paginate {
                        color: inherit !important;
                        margin-bottom: 0.75rem;
                        margin-top: 0.75rem;
                    }

                    /* Styling Button Pagination DataTables */
                    .dataTables_wrapper .dataTables_paginate .paginate_button {
                        border-radius: 0.5rem !important;
                        padding: 0.3rem 0.8rem !important;
                        margin: 0 0.2rem !important;
                        border: 1px solid transparent !important;
                        color: #64748b !important;
                        transition: all 0.2s ease;
                    }

                    .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
                        color: #94a3b8 !important;
                    }

                    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                        background: #f1f5f9 !important;
                        color: #334155 !important;
                        border-color: #e2e8f0 !important;
                    }

                    .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                        background: #334155 !important;
                        color: #f8fafc !important;
                        border-color: #475569 !important;
                    }

                    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
                    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                        background: #e0e7ff !important;
                        color: #4f46e5 !important;
                        border-color: #c7d2fe !important;
                        font-weight: bold;
                    }

                    .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current,
                    .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                        background: #312e81 !important;
                        color: #818cf8 !important;
                        border-color: #3730a3 !important;
                    }

                    /* Menghilangkan garis hitam tebal dari jquery-datatables */
                    table.dataTable.no-footer {
                        border-bottom: 1px solid #e2e8f0 !important;
                    }

                    .dark table.dataTable.no-footer {
                        border-bottom: 1px solid #334155 !important;
                    }

                    table.dataTable thead th,
                    table.dataTable thead td {
                        border-bottom: 1px solid #e2e8f0 !important;
                    }

                    .dark table.dataTable thead th,
                    .dark table.dataTable thead td {
                        border-bottom: 1px solid #334155 !important;
                    }
                </style>

                <div class="overflow-x-auto px-6 pb-6 pt-4">
                    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <button id="bulkReturnBtn" type="button" disabled
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition opacity-50">
                                Kembalikan Terpilih
                            </button>
                            <button id="bulkDeleteBtn" type="button" disabled
                                class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-bold hover:bg-rose-700 transition opacity-50">
                                Hapus Terpilih
                            </button>
                        </div>
                    </div>

                    <table id="bookLoansTable"
                        class="w-full text-sm text-left text-slate-500 dark:text-slate-400 min-w-[900px]">
                        <thead
                            class="text-xs text-slate-700 uppercase bg-slate-100/50 dark:bg-slate-900/50 dark:text-slate-300">
                            <tr>
                                <th class="px-3 py-4 w-12 text-center">
                                    <input type="checkbox" id="selectAllLoans"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer" />
                                </th>
                                <th class="px-6 py-4">Nama Siswa</th>
                                <th class="px-6 py-4">Judul Buku</th>
                                <th class="px-6 py-4 whitespace-nowrap">Tgl Pinjam</th>
                                <th class="px-6 py-4 whitespace-nowrap">Jatuh Tempo</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- PENGGUNAAN FOREACH (bukan forelse) agar Datatables tidak error perhitungan saat data
                            kosong --}}
                            @foreach($loans as $loan)
                            <tr
                                class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-3 py-4 text-center">
                                    <input type="checkbox"
                                        class="loan-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer"
                                        value="{{ $loan->id }}" />
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $loan->student->nama_lengkap ?? '—' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300">
                                    {{ $loan->book_title }}
                                </td>
                                <td class="px-6 py-4 text-xs whitespace-nowrap">
                                    @if($loan->borrowed_at){{ \Carbon\Carbon::parse($loan->borrowed_at)->format('d M Y,
                                    H:i') }}@else — @endif
                                </td>
                                <td class="px-6 py-4 text-xs whitespace-nowrap">
                                    @if($loan->due_at){{ \Carbon\Carbon::parse($loan->due_at)->format('d M Y') }}@else —
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($loan->returned_at)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">Kembali</span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">Dipinjam</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if(!$loan->returned_at)
                                        <form action="{{ route('book-loans.return', $loan) }}" method="POST"
                                            onsubmit="return confirm('Tandai buku ini sebagai dikembalikan?');"
                                            class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="px-2 py-1 text-[10px] font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 uppercase">Kembalikan</button>
                                        </form>
                                        @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-100">Selesai</span>
                                        @endif

                                        <form action="{{ route('book-loans.destroy', $loan) }}" method="POST"
                                            onsubmit="return confirm('Hapus catatan peminjaman ini?');"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2 py-1 text-[10px] font-bold text-white bg-rose-600 rounded-lg hover:bg-rose-700 uppercase">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <form id="bulkReturnForm" action="{{ route('book-loans.return-multiple') }}" method="POST"
                        class="hidden">
                        @csrf
                    </form>
                    <form id="bulkDeleteForm" action="{{ route('book-loans.delete-multiple') }}" method="POST"
                        class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const studentSelect = document.getElementById('student_select');
            const tingkatSelect = document.getElementById('tingkat_select');
            const bookSelect = document.getElementById('book_select');
            const selectAllLoans = $('#selectAllLoans');
            const bulkReturnBtn = $('#bulkReturnBtn');
            const bulkDeleteBtn = $('#bulkDeleteBtn');
            const bulkReturnForm = $('#bulkReturnForm');
            const bulkDeleteForm = $('#bulkDeleteForm');

            if (document.getElementById('select_all_students') && studentSelect) {
                document.getElementById('select_all_students').addEventListener('change', function (e) {
                    const selected = e.target.checked;
                    Array.from(studentSelect.options).forEach(opt => opt.selected = selected);
                });
            }

            function filterBooks() {
                const tingkat = tingkatSelect.value;
                Array.from(bookSelect.options).forEach(opt => {
                    if (opt.value === '') { opt.style.display = ''; return; }
                    const t = opt.dataset.tingkat || '';
                    opt.style.display = (tingkat === '' || String(t) === String(tingkat)) ? '' : 'none';
                });

                if (bookSelect.selectedOptions.length && bookSelect.selectedOptions[0].style.display === 'none') {
                    bookSelect.value = '';
                }
            }

            if (studentSelect) {
                studentSelect.addEventListener('change', function (e) {
                    const opt = e.target.selectedOptions[0];
                    const t = opt ? opt.dataset.tingkat : '';
                    if (t) {
                        tingkatSelect.value = t;
                    }
                    filterBooks();
                });
                const ev = new Event('change');
                studentSelect.dispatchEvent(ev);
            }

            if (tingkatSelect) {
                tingkatSelect.addEventListener('change', filterBooks);
            }

            filterBooks();

            function getSelectedLoanIds() {
                return $('.loan-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();
            }

            function updateBulkButtons() {
                const enabled = getSelectedLoanIds().length > 0;
                bulkReturnBtn.prop('disabled', !enabled).toggleClass('opacity-50', !enabled);
                bulkDeleteBtn.prop('disabled', !enabled).toggleClass('opacity-50', !enabled);
            }

            function syncSelectAllState() {
                const total = $('.loan-checkbox').length;
                const checked = $('.loan-checkbox:checked').length;
                selectAllLoans.prop('checked', checked > 0 && checked === total);
                selectAllLoans.prop('indeterminate', checked > 0 && checked < total);
            }

            function submitBulkForm(form, selectedIds) {
                if (!form.length || selectedIds.length === 0) return;
                form.find('input[name="loan_ids[]"]').remove();
                selectedIds.forEach(id => {
                    form.append('<input type="hidden" name="loan_ids[]" value="' + id + '">');
                });
                const nativeForm = form.get(0);
                if (nativeForm) {
                    nativeForm.submit();
                }
            }

            selectAllLoans.on('change', function () {
                $('.loan-checkbox').prop('checked', this.checked);
                updateBulkButtons();
            });

            $('#bookLoansTable tbody').on('change', '.loan-checkbox', function () {
                syncSelectAllState();
                updateBulkButtons();
            });

            bulkReturnBtn.on('click', function () {
                const selectedIds = getSelectedLoanIds();
                if (selectedIds.length === 0) return;
                if (confirm('Tandai pinjaman yang dipilih sebagai dikembalikan?')) {
                    submitBulkForm(bulkReturnForm, selectedIds);
                }
            });

            bulkDeleteBtn.on('click', function () {
                const selectedIds = getSelectedLoanIds();
                if (selectedIds.length === 0) return;
                if (confirm('Hapus catatan peminjaman yang dipilih?')) {
                    submitBulkForm(bulkDeleteForm, selectedIds);
                }
            });

            updateBulkButtons();
            syncSelectAllState();

            // INSTANSIASI DATATABLES DENGAN PENYEMPURNAAN UI TAILWIND
           // INSTANSIASI DATATABLES DENGAN PENYEMPURNAAN UI TAILWIND
            if ($.fn.DataTable) {
                $('#bookLoansTable').DataTable({
                    columnDefs: [{ orderable: false, targets: [0, 6] }],
                    pageLength: 10,
                    lengthMenu: [10, 20, 50, 100],
                    language: {
                        search: 'Cari Data:',
                        lengthMenu: 'Tampilkan _MENU_ baris',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        paginate: {
                            first: 'Awal',
                            last: 'Akhir',
                            next: 'Maju',
                            previous: 'Mundur',
                        },
                        zeroRecords: 'Tidak ada data yang cocok ditemukan.',
                        emptyTable: 'Belum ada catatan peminjaman buku saat ini.',
                    },
             initComplete: function() {
                        // PERBAIKAN FINAL: Tambahkan w-20 (lebar fix) dan pr-10 (padding kanan diperbesar)
                        $('select[name="bookLoansTable_length"]').addClass('rounded-lg border-slate-300 bg-white dark:bg-slate-800 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 mx-2 py-1.5 pl-3 pr-10 w-20 outline-none shadow-sm cursor-pointer');

                        // Injeksi class Tailwind ke dalam input pencarian
                        $('.dataTables_filter input').addClass('rounded-lg border-slate-300 bg-white dark:bg-slate-800 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 ml-2 py-1.5 px-3 outline-none shadow-sm');

                        // Rapikan warna teks
                        $('.dataTables_info, .dataTables_length, .dataTables_filter').addClass('text-sm text-slate-600 dark:text-slate-400');
                    }
                });
            }
        });
    </script>
</x-app-layout>