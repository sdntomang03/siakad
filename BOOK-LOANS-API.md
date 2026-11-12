# API Peminjaman Buku

Endpoint berikut memakai autentikasi Sanctum. Klien API mengirim token pada header `Authorization: Bearer <token>`. Halaman SIAKAD memakai sesi web yang sudah masuk. Semua respons menggunakan JSON.

| Method | Endpoint | Kegunaan |
| --- | --- | --- |
| `GET` | `/api/book-loans` | Daftar peminjaman, dengan pagination. Parameter `per_page` dibatasi maksimal 100. |
| `GET` | `/api/book-loans/options` | Daftar siswa dan katalog buku yang dapat dipilih pengguna. |
| `POST` | `/api/book-loans` | Mencatat peminjaman. |
| `PATCH` | `/api/book-loans/{id}/return` | Mencatat pengembalian. Permintaan berulang tidak mengubah waktu pengembalian pertama. |

Contoh body untuk mencatat peminjaman:

```json
{
  "student_id": 1,
  "book_id": 2,
  "due_at": "2026-11-26"
}
```

`book_id` dapat diganti dengan `book_title` untuk buku yang belum ada di katalog. `borrowed_at` dan `due_at` bersifat opsional. Jika `due_at` tidak dikirim, sistem menetapkannya 14 hari setelah tanggal pinjam. Siswa tidak dapat meminjam buku yang sama lebih dari sekali selama pinjaman sebelumnya masih aktif.

Data siswa dan buku dibatasi ke sekolah pengguna. Guru hanya dapat mengelola siswa pada kelas wali di tahun ajaran aktif. `401` berarti autentikasi diperlukan, `404` berarti data tidak tersedia untuk pengguna tersebut, `409` menandakan konflik pinjaman aktif, dan `422` berisi kesalahan validasi.
