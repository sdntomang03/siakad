<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-950 dark:text-slate-100">
                    Monitoring Peminjaman Siswa
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                    Catat peminjaman dan konfirmasi pengembalian dari daftar siswa.
                </p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                <button id="openBorrowDialog" type="button"
                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700">
                    Catat peminjaman
                </button>
                <a href="{{ route('book-loans.export-unreturned') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700 dark:active:bg-slate-600">
                    Export Excel
                </a>
                <a href="{{ route('book-loans.index') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-5xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div id="feedback" role="status" aria-live="polite" hidden
                class="rounded-md border p-4 text-sm font-medium"></div>

            <div class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <label for="searchInput" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                    Cari nama siswa
                </label>
                <input type="search" id="searchInput" placeholder="Ketik nama siswa..."
                    class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm transition-colors hover:border-slate-400 active:border-brand-700 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:hover:border-slate-500">
            </div>

            <p id="noSearchResults" class="rounded-lg border border-slate-200 bg-white p-6 text-center text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200" hidden>
                Tidak ada siswa yang cocok dengan pencarian.
            </p>

            <div class="space-y-3" id="studentList">
                @forelse($students as $student)
                    @php
                        $activeLoansCount = $student->bookLoans->count();
                        $kelas = $student->kelasAktif();
                    @endphp

                    <section x-data="{ open: false }" data-student-id="{{ $student->id }}"
                        data-school-id="{{ $student->school_id }}"
                        class="student-card overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800"
                        data-name="{{ strtolower($student->nama_lengkap) }}">
                        <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                            aria-controls="loan-details-{{ $student->id }}"
                            class="flex min-h-16 w-full items-center justify-between gap-3 p-4 text-left transition-colors hover:bg-slate-50 active:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-brand-700 dark:hover:bg-slate-700/50 dark:active:bg-slate-700 sm:p-5">
                            <span class="flex min-w-0 items-center gap-3 sm:gap-4">
                                <span aria-hidden="true"
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-brand-100 text-lg font-semibold text-brand-800 dark:bg-brand-900/50 dark:text-brand-200">
                                    {{ substr($student->nama_lengkap, 0, 1) }}
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold text-slate-950 dark:text-slate-100 md:text-base">
                                        {{ $student->nama_lengkap }}
                                    </span>
                                    <span class="block text-xs text-slate-600 dark:text-slate-300">
                                        {{ $kelas ? 'Kelas '.$kelas->tingkat.' '.$kelas->nama_kelas : 'Belum masuk kelas' }}
                                    </span>
                                </span>
                            </span>

                            <span class="flex shrink-0 items-center gap-2 sm:gap-4">
                                <span data-loan-summary
                                    class="{{ $activeLoansCount ? 'border-rose-200 bg-rose-100 text-rose-800 dark:border-rose-800 dark:bg-rose-900/30 dark:text-rose-300' : 'border-slate-300 bg-slate-100 text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200' }} rounded-md border px-2 py-1 text-xs font-semibold">
                                    {{ $activeLoansCount ? $activeLoansCount.' buku dipinjam' : 'Tidak ada tanggungan' }}
                                </span>
                                <svg :class="open ? 'rotate-180 text-brand-600' : 'text-slate-500'"
                                    class="h-5 w-5 transition-transform duration-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </button>

                        <div id="loan-details-{{ $student->id }}" x-show="open" x-collapse
                            class="border-t border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/50"
                            style="display: none;">
                            <div class="space-y-4 p-4 sm:p-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <h3 class="font-semibold text-slate-900 dark:text-slate-100">Buku yang dipinjam</h3>
                                    <button type="button" data-borrow-student="{{ $student->id }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-md border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-800 transition hover:bg-brand-50 active:bg-brand-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-brand-500 dark:text-brand-200 dark:hover:bg-slate-800">
                                        Catat peminjaman
                                    </button>
                                </div>

                                <p data-loan-empty @if($activeLoansCount) hidden @endif
                                    class="rounded-md border border-slate-200 bg-white p-5 text-center text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                    Siswa ini tidak memiliki pinjaman aktif.
                                </p>

                                <div data-loan-table @if(! $activeLoansCount) hidden @endif
                                    class="overflow-x-auto rounded-md border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                                    <table class="min-w-[36rem] w-full text-left text-sm text-slate-700 dark:text-slate-200">
                                        <thead class="bg-slate-100 text-xs text-slate-800 dark:bg-slate-900/80 dark:text-slate-200">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">Judul Buku</th>
                                                <th scope="col" class="whitespace-nowrap px-4 py-3">Tgl Pinjam</th>
                                                <th scope="col" class="whitespace-nowrap px-4 py-3">Jatuh Tempo</th>
                                                <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody data-student-loans="{{ $student->id }}"
                                            class="divide-y divide-slate-200 dark:divide-slate-700">
                                            @foreach($student->bookLoans as $loan)
                                                <tr data-loan-row="{{ $loan->id }}" data-book-id="{{ $loan->book_id }}">
                                                    <td data-loan-title class="px-4 py-3 font-semibold">
                                                        {{ $loan->book_title }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-xs">
                                                        {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('d M Y') }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-xs">
                                                        @if($loan->due_at)
                                                            @php $isLate = \Carbon\Carbon::parse($loan->due_at)->isPast(); @endphp
                                                            <span class="{{ $isLate ? 'font-semibold text-rose-700 dark:text-rose-300' : '' }}">
                                                                {{ \Carbon\Carbon::parse($loan->due_at)->format('d M Y') }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                                        <button type="button" data-return-loan="{{ $loan->id }}"
                                                            class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                                                            Terima Buku
                                                        </button>
                                                        <span data-returned-label hidden
                                                            class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                            Dikembalikan
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                @empty
                    <p class="rounded-lg border border-slate-200 bg-white p-6 text-center text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        Tidak ada data siswa untuk ditampilkan.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    <dialog id="borrowDialog" aria-labelledby="borrowDialogTitle"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-lg border border-slate-300 bg-white p-0 text-slate-900 shadow-xl backdrop:bg-slate-950/50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
        <form id="borrowForm" class="space-y-5 p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="borrowDialogTitle" class="text-lg font-semibold">Catat peminjaman buku</h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                        Pilih siswa dan buku. Jatuh tempo otomatis diatur 14 hari setelah tanggal pinjam.
                    </p>
                </div>
                <button type="button" data-close-dialog aria-label="Tutup formulir peminjaman"
                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-md border border-slate-300 text-xl text-slate-700 transition hover:bg-slate-100 active:bg-slate-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <p id="optionsLoading" role="status" class="text-sm text-slate-700 dark:text-slate-200">
                Memuat daftar siswa dan buku...
            </p>
            <p id="borrowError" role="alert" hidden
                class="rounded-md border border-rose-300 bg-rose-50 p-3 text-sm text-rose-900 dark:border-rose-800 dark:bg-rose-950 dark:text-rose-200"></p>

            <div class="space-y-4">
                <div>
                    <label for="borrowStudent" class="mb-1.5 block text-sm font-medium">Siswa</label>
                    <select id="borrowStudent" name="student_id" required disabled
                        class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm transition-colors enabled:hover:border-slate-400 enabled:active:border-brand-700 focus:border-brand-600 focus:ring-brand-600 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-600 dark:bg-slate-900 dark:enabled:hover:border-slate-500 dark:disabled:bg-slate-700">
                        <option value="">Pilih siswa</option>
                    </select>
                </div>

                <div>
                    <label for="borrowBook" class="mb-1.5 block text-sm font-medium">Buku dari katalog</label>
                    <select id="borrowBook" name="book_id" disabled
                        class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm transition-colors enabled:hover:border-slate-400 enabled:active:border-brand-700 focus:border-brand-600 focus:ring-brand-600 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-slate-600 dark:bg-slate-900 dark:enabled:hover:border-slate-500 dark:disabled:bg-slate-700">
                        <option value="">Pilih buku atau isi judul secara manual</option>
                    </select>
                </div>

                <div>
                    <label for="borrowBookTitle" class="mb-1.5 block text-sm font-medium">Judul buku jika tidak ada di katalog</label>
                    <input id="borrowBookTitle" name="book_title" type="text" maxlength="255"
                        placeholder="Masukkan judul buku"
                        class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm transition-colors hover:border-slate-400 active:border-brand-700 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:hover:border-slate-500">
                </div>

                <div>
                    <label for="borrowDueAt" class="mb-1.5 block text-sm font-medium">Jatuh tempo (opsional)</label>
                    <input id="borrowDueAt" name="due_at" type="date"
                        class="min-h-11 w-full rounded-md border-slate-300 bg-white text-sm transition-colors hover:border-slate-400 active:border-brand-700 focus:border-brand-600 focus:ring-brand-600 dark:border-slate-600 dark:bg-slate-900 dark:hover:border-slate-500">
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" data-close-dialog
                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                    Batal
                </button>
                <button id="submitBorrow" type="submit" disabled
                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                    Simpan peminjaman
                </button>
            </div>
        </form>
    </dialog>

    <script>
        (() => {
            const apiBase = @json(url('/api/book-loans'));
            const optionsUrl = @json(route('api.book-loans.options'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const dialog = document.getElementById('borrowDialog');
            const form = document.getElementById('borrowForm');
            const studentSelect = document.getElementById('borrowStudent');
            const bookSelect = document.getElementById('borrowBook');
            const titleInput = document.getElementById('borrowBookTitle');
            const submitButton = document.getElementById('submitBorrow');
            const optionsLoading = document.getElementById('optionsLoading');
            const errorElement = document.getElementById('borrowError');
            const feedback = document.getElementById('feedback');
            let options = null;
            let returnCount = 0;

            async function requestJson(url, request = {}) {
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    ...request,
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        ...request.headers,
                    },
                });
                let result;
                try {
                    result = await response.json();
                } catch {
                    throw new Error('Respons server tidak dapat dibaca. Coba lagi.');
                }
                if (!response.ok) {
                    const validationMessage = Object.values(result.errors || {}).flat().join(' ');
                    throw new Error(validationMessage || result.message || 'Permintaan tidak dapat diproses.');
                }

                return result.data;
            }

            function showFeedback(message, isError = false) {
                feedback.hidden = false;
                feedback.textContent = message;
                feedback.className = isError
                    ? 'rounded-md border border-rose-300 bg-rose-50 p-4 text-sm font-medium text-rose-900 dark:border-rose-800 dark:bg-rose-950 dark:text-rose-200'
                    : 'rounded-md border border-emerald-300 bg-emerald-50 p-4 text-sm font-medium text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200';
            }

            function showBorrowError(message) {
                errorElement.hidden = false;
                errorElement.textContent = message;
            }

            function formatDate(value) {
                if (!value) {
                    return '-';
                }

                return new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                }).format(new Date(value));
            }

            function updateLoanSummary(card, count) {
                const summary = card.querySelector('[data-loan-summary]');
                summary.textContent = count ? `${count} buku dipinjam` : 'Tidak ada tanggungan';
                summary.className = count
                    ? 'rounded-md border border-rose-200 bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-800 dark:border-rose-800 dark:bg-rose-900/30 dark:text-rose-300'
                    : 'rounded-md border border-slate-300 bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200';
            }

            function appendLoan(loan) {
                const card = document.querySelector(`[data-student-id="${loan.student_id}"]`);
                if (!card) {
                    return;
                }

                const table = card.querySelector('[data-loan-table]');
                const emptyState = card.querySelector('[data-loan-empty]');
                const tbody = card.querySelector(`[data-student-loans="${loan.student_id}"]`);
                const row = document.createElement('tr');
                row.dataset.loanRow = loan.id;
                row.dataset.bookId = loan.book_id ?? '';

                const titleCell = document.createElement('td');
                titleCell.dataset.loanTitle = '';
                titleCell.className = 'px-4 py-3 font-semibold';
                titleCell.textContent = loan.book_title;

                const borrowedCell = document.createElement('td');
                borrowedCell.className = 'whitespace-nowrap px-4 py-3 text-xs';
                borrowedCell.textContent = formatDate(loan.borrowed_at);

                const dueCell = document.createElement('td');
                dueCell.className = 'whitespace-nowrap px-4 py-3 text-xs';
                dueCell.textContent = formatDate(loan.due_at);

                const actionCell = document.createElement('td');
                actionCell.className = 'whitespace-nowrap px-4 py-3 text-right';
                const returnButton = document.createElement('button');
                returnButton.type = 'button';
                returnButton.dataset.returnLoan = loan.id;
                returnButton.className = 'inline-flex min-h-11 items-center justify-center rounded-md bg-brand-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-brand-800 active:bg-brand-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:cursor-not-allowed disabled:opacity-60';
                returnButton.textContent = 'Terima Buku';

                const returnedLabel = document.createElement('span');
                returnedLabel.dataset.returnedLabel = '';
                returnedLabel.hidden = true;
                returnedLabel.className = 'text-xs font-semibold text-slate-700 dark:text-slate-200';
                returnedLabel.textContent = 'Dikembalikan';

                actionCell.append(returnButton, returnedLabel);
                row.append(titleCell, borrowedCell, dueCell, actionCell);
                tbody.append(row);
                table.hidden = false;
                emptyState.hidden = true;
                updateLoanSummary(card, card.querySelectorAll('[data-return-loan]').length);
            }

            function updateBookChoices() {
                const student = options?.students.find((item) => String(item.id) === studentSelect.value);
                const previousBookId = bookSelect.value;
                bookSelect.replaceChildren(new Option('Pilih buku atau isi judul secara manual', ''));

                if (!student) {
                    bookSelect.disabled = true;
                    return;
                }

                const books = options.books.filter((book) => String(book.school_id) === String(student.school_id));
                books.forEach((book) => {
                    bookSelect.add(new Option(book.title, book.id));
                });
                bookSelect.disabled = false;
                if (books.some((book) => String(book.id) === previousBookId)) {
                    bookSelect.value = previousBookId;
                }
            }

            async function loadOptions(preselectedStudentId = '') {
                errorElement.hidden = true;
                optionsLoading.hidden = false;
                submitButton.disabled = true;
                studentSelect.disabled = true;
                bookSelect.disabled = true;

                try {
                    options = options || await requestJson(optionsUrl);
                    studentSelect.replaceChildren(new Option('Pilih siswa', ''));
                    options.students.forEach((student) => {
                        const label = student.classroom
                            ? `${student.name} (${student.classroom})`
                            : student.name;
                        studentSelect.add(new Option(label, student.id));
                    });
                    studentSelect.disabled = options.students.length === 0;
                    submitButton.disabled = options.students.length === 0;
                    studentSelect.value = preselectedStudentId;
                    updateBookChoices();
                    optionsLoading.textContent = options.students.length
                        ? 'Daftar siswa dan buku siap digunakan.'
                        : 'Tidak ada siswa yang dapat dipilih.';
                    optionsLoading.hidden = options.students.length > 0;
                } catch (error) {
                    optionsLoading.hidden = true;
                    showBorrowError(error.message);
                }
            }

            document.getElementById('openBorrowDialog').addEventListener('click', async () => {
                form.reset();
                dialog.showModal();
                await loadOptions();
            });

            document.querySelectorAll('[data-borrow-student]').forEach((button) => {
                button.addEventListener('click', async () => {
                    form.reset();
                    dialog.showModal();
                    await loadOptions(button.dataset.borrowStudent);
                });
            });

            document.querySelectorAll('[data-close-dialog]').forEach((button) => {
                button.addEventListener('click', () => dialog.close());
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && dialog.open) {
                    event.preventDefault();
                    dialog.close();
                }
            });

            studentSelect.addEventListener('change', updateBookChoices);
            bookSelect.addEventListener('change', () => {
                if (bookSelect.value) {
                    titleInput.value = '';
                }
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                errorElement.hidden = true;
                const payload = Object.fromEntries(new FormData(form).entries());
                if (!payload.book_id && !payload.book_title.trim()) {
                    showBorrowError('Pilih buku dari katalog atau isi judul buku.');
                    titleInput.focus();
                    return;
                }
                const studentCard = [...document.querySelectorAll('.student-card')]
                    .find((card) => card.dataset.studentId === payload.student_id);
                const selectedBook = payload.book_id
                    ? options.books.find((book) => String(book.id) === payload.book_id)
                    : null;
                const requestedTitle = (selectedBook?.title || payload.book_title).trim().toLocaleLowerCase('id');
                const duplicate = studentCard && [...studentCard.querySelectorAll('[data-return-loan]')].some((button) => {
                    const row = button.closest('[data-loan-row]');
                    return payload.book_id
                        ? row.dataset.bookId === payload.book_id
                        : !row.dataset.bookId
                            && row.querySelector('[data-loan-title]').textContent.trim().toLocaleLowerCase('id') === requestedTitle;
                });
                if (duplicate) {
                    showBorrowError('Siswa masih memiliki pinjaman aktif untuk buku tersebut.');
                    return;
                }
                if (payload.book_id) {
                    delete payload.book_title;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';
                try {
                    const loan = await requestJson(apiBase, {
                        method: 'POST',
                        body: JSON.stringify(payload),
                    });
                    appendLoan(loan);
                    dialog.close();
                    form.reset();
                    updateBookChoices();
                    showFeedback('Peminjaman berhasil dicatat.');
                } catch (error) {
                    showBorrowError(error.message);
                } finally {
                    submitButton.textContent = 'Simpan peminjaman';
                    submitButton.disabled = !options?.students.length;
                }
            });

            document.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-return-loan]');
                if (!button) {
                    return;
                }

                const loanId = button.dataset.returnLoan;
                button.disabled = true;
                button.textContent = 'Memproses...';
                try {
                    await requestJson(`${apiBase}/${loanId}/return`, { method: 'PATCH' });
                    const row = button.closest('[data-loan-row]');
                    const card = button.closest('[data-student-id]');
                    row.querySelector('[data-loan-title]').classList.add('line-through', 'text-slate-500');
                    const activeCount = Math.max(0, card.querySelectorAll('[data-return-loan]').length - 1);
                    button.remove();
                    row.querySelector('[data-returned-label]').hidden = false;
                    updateLoanSummary(card, activeCount);
                    showFeedback('Pengembalian berhasil dicatat.');
                } catch (error) {
                    button.disabled = false;
                    button.textContent = 'Terima Buku';
                    showFeedback(error.message, true);
                }
            });

            document.getElementById('searchInput').addEventListener('input', (event) => {
                const searchTerm = event.target.value.trim().toLocaleLowerCase('id');
                const cards = [...document.querySelectorAll('.student-card')];
                let visibleCount = 0;

                cards.forEach((card) => {
                    const matches = card.dataset.name.includes(searchTerm);
                    card.hidden = !matches;
                    visibleCount += Number(matches);
                });
                document.getElementById('noSearchResults').hidden = visibleCount > 0;
            });
        })();
    </script>
</x-app-layout>
