<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Deteksi jika ada session 'success'
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                showConfirmButton: false,
                timer: 2500,
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700'
                }
            });
        @endif

        // Deteksi jika ada session 'error'
@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Kesalahan Input',
        html: '{!! implode("<br>", $errors->all()) !!}', // Menampilkan semua error validasi
        background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
        color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
    });
@endif
    });

      function confirmAction(formId, title, text) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5', // Warna tombol Indigo-600 (Sesuai tema)
            cancelButtonColor: '#94a3b8', // Warna tombol Batal (Slate-400)
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            // Deteksi Dark Mode otomatis
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700'
            }
        }).then((result) => {
            // Jika pengguna klik "Ya, Lanjutkan!"
            if (result.isConfirmed) {
                // Submit formulir secara manual
                document.getElementById(formId).submit();
            }
        });
    }

</script>