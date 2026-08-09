<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookLoanController;
use App\Http\Controllers\CapaianPembelajaranController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EtppController;
use App\Http\Controllers\ExamGradeController;
use App\Http\Controllers\FinalGradeController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GradeCurveController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\IjazahController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\KelulusanController;
use App\Http\Controllers\ModulAjarController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\Operator\UserController as OperatorUserController;
use App\Http\Controllers\PiketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RenkinController;
use App\Http\Controllers\ReportSubmissionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentFinalNoteController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SuperAdmin\RolePermissionController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\TeacherNoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');

});
Route::get('/pengumuman', [KelulusanController::class, 'portalPengumuman'])->name('pengumuman.index');
// Route untuk download PDF Surat Keterangan Lulus
Route::get('/pengumuman/download-skl/{token}', [KelulusanController::class, 'downloadSKL'])
    ->name('kelulusan.download');

// Route untuk landing page ketika QR Code di-scan
Route::get('/validasi-dokumen/{token}', [KelulusanController::class, 'validasiSKL'])
    ->name('kelulusan.validasi');
// 2. Memproses Data dari Form
Route::post('/pengumuman/cek', [KelulusanController::class, 'cekKelulusan'])
    ->name('kelulusan.cek');

// 3. Menampilkan Halaman Hasil (Hanya terbuka jika data disubmit)
Route::get('/pengumuman/hasil', [KelulusanController::class, 'halamanHasil'])
    ->name('kelulusan.hasil');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('students', StudentController::class)->only(['edit', 'update']);
    Route::resource('employees', EmployeeController::class)->only(['edit', 'update']);
});

Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('superadmin.')->group(function () {

    // URL: /admin/dashboard
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // URL: /admin/schools
    Route::resource('schools', SchoolController::class);

    // Rute Reset Password
    Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Rute CRUD User
    Route::resource('users', UserController::class);
    // RUTE BARU UNTUK MANAJEMEN ROLE:
    Route::get('roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::get('roles/{role}/edit', [RolePermissionController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
    Route::post('permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
    Route::delete('permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('permissions.destroy');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/modul-generator', [ModulAjarController::class, 'index'])->name('modul.generator');
    Route::post('/modul-generator/store', [ModulAjarController::class, 'store'])->name('modul.store');
    // Rute Khusus Aktifkan Semester
    Route::patch('/academic-years/{academicYear}/aktifkan', [AcademicYearController::class, 'aktifkan'])
        ->name('academic-years.aktifkan')
        ->middleware('permission:edit-academic-years');

    // Rute CRUD Resource (Index, Store, Update, Destroy)
    Route::resource('academic-years', AcademicYearController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('classrooms', ClassroomController::class);
    Route::post('classrooms/{classroom}/assign-subjects', [ClassroomController::class, 'assignSubjectTeacher'])
        ->name('classrooms.assign-subjects');
    Route::post('classrooms/{classroom}/assign', [ClassroomController::class, 'assignStudent'])->name('classrooms.assign');
    Route::delete('classrooms/{classroom}/remove-multiple', [ClassroomController::class, 'removeMultipleStudents'])->name('classrooms.remove-multiple');
    Route::get('classrooms/{classroom}/attendances', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::post('classrooms/{classroom}/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('students/{student}/attendance-report', [AttendanceController::class, 'studentReport'])->name('attendances.student-report');
    Route::get('attendances/report', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/monthly', [AttendanceController::class, 'monthlyRecap'])->name('attendances.monthly');
    Route::get('/attendances/monthly/pdf', [AttendanceController::class, 'downloadPdf'])->name('attendances.monthly.pdf');
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
    Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
    Route::get('teacher-notes', [TeacherNoteController::class, 'index'])->name('teacher-notes.index');
    Route::post('teacher-notes', [TeacherNoteController::class, 'store'])->name('teacher-notes.store');
    Route::delete('teacher-notes/{id}', [TeacherNoteController::class, 'destroy'])->name('teacher-notes.destroy');
    Route::get('teacher-notes/report', [TeacherNoteController::class, 'report'])->name('teacher-notes.report');
    Route::put('/teacher-notes/{id}', [TeacherNoteController::class, 'update'])->name('teacher-notes.update');
});

Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {

    // Rute Reset Password
    Route::patch('users/{user}/reset-password', [OperatorUserController::class, 'resetPassword'])
        ->name('users.reset-password')
        ->middleware('permission:edit-users');
    Route::post('users/import', [OperatorUserController::class, 'import'])->name('users.import');
    Route::delete('users/bulk-destroy', [OperatorUserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    // Rute CRUD User Guru
    Route::resource('users', OperatorUserController::class)->except(['create', 'edit', 'show']);

});

Route::middleware(['auth', 'role:superadmin|operator'])->group(function () {
    Route::delete('/subjects/bulk-delete', [SubjectController::class, 'bulkDestroy'])->name('subjects.bulkDestroy');
    Route::patch('/subjects/bulk-update-urutan', [SubjectController::class, 'bulkUpdateUrutan'])->name('subjects.bulk-update-urutan');
    Route::resource('subjects', SubjectController::class)->except(['create', 'show', 'edit']);
    Route::get('kelulusan/import', [KelulusanController::class, 'showImportForm'])->name('kelulusan.import');
    Route::post('kelulusan/import', [KelulusanController::class, 'importExcel'])->name('kelulusan.import.process');
    Route::delete('/dashboard/kelulusan/kosongkan', [KelulusanController::class, 'deleteAll'])->name('kelulusan.delete-all');
    Route::post('kelulusan/{id}/update-status', [KelulusanController::class, 'updateStatusAjax'])->name('kelulusan.update_status');
    Route::resource('books', BookController::class)->except(['show']);
});

// Routes for book loans and report submissions
Route::middleware(['auth'])->group(function () {
    Route::get('book-loans', [BookLoanController::class, 'index'])->name('book-loans.index');
    Route::post('book-loans', [BookLoanController::class, 'store'])->name('book-loans.store');
    Route::get('book-loans/export-unreturned', [BookLoanController::class, 'exportUnreturned'])->name('book-loans.export-unreturned');
    Route::post('book-loans/{bookLoan}/return', [BookLoanController::class, 'markReturned'])->name('book-loans.return');
    Route::delete('book-loans/{bookLoan}', [BookLoanController::class, 'destroy'])->name('book-loans.destroy');
    Route::get('book-loans/monitor', [BookLoanController::class, 'monitor'])->name('book-loans.monitor');
    // Bulk actions
    Route::post('book-loans/return-multiple', [BookLoanController::class, 'returnMultiple'])->name('book-loans.return-multiple');
    Route::post('book-loans/delete-multiple', [BookLoanController::class, 'destroyMultiple'])->name('book-loans.delete-multiple');
    // Friendly GET redirect to index for delete-multiple path
    Route::get('book-loans/delete-multiple', function () {
        return redirect()->route('book-loans.index')->with('warning', 'Gunakan tombol "Hapus Terpilih" di halaman Riwayat Peminjaman untuk menghapus.');
    });

    Route::get('report-submissions', [ReportSubmissionController::class, 'index'])
        ->name('report-submissions.index');

    Route::post('report-submissions/bulk-update', [ReportSubmissionController::class, 'bulkUpdate'])
        ->name('report-submissions.bulk-update');

    Route::post('report-submissions/{reportSubmission}/toggle', [ReportSubmissionController::class, 'toggleStatus'])
        ->name('report-submissions.toggle');

    Route::delete('report-submissions/{reportSubmission}', [ReportSubmissionController::class, 'destroy'])
        ->name('report-submissions.destroy');
    Route::post('report-submissions/bulk-update-history', [ReportSubmissionController::class, 'bulkUpdateHistory'])->name('report-submissions.bulk-update-history');
    Route::post('report-submissions/bulk-destroy-history', [ReportSubmissionController::class, 'bulkDestroyHistory'])->name('report-submissions.bulk-destroy-history');

    Route::post('admin/assets', [AssetController::class, 'store'])->name('admin.assets.store');
    Route::get('admin/asset-tracking', [AssetController::class, 'index'])->name('admin.asset-tracking.index');
    Route::get('admin/assets/list', [AssetController::class, 'listMasterAssets'])->name('admin.assets.list');
    Route::patch('admin/assets/{asset}/approve', [AssetController::class, 'approve'])->name('admin.assets.approve');
    Route::patch('admin/assets/{asset}/reject', [AssetController::class, 'reject'])->name('admin.assets.reject');
    Route::get('admin/assets/{asset}/edit', [AssetController::class, 'edit'])->name('admin.assets.edit');
    Route::put('admin/assets/{asset}', [AssetController::class, 'update'])->name('admin.assets.update');
    Route::delete('admin/assets/{asset}', [AssetController::class, 'destroy'])->name('admin.assets.destroy');
    Route::resource('admin/rooms', RoomController::class)->except(['create', 'show', 'edit']);
    Route::get('admin/rooms/detail/{type}/{id}', [RoomController::class, 'showAssets'])->name('rooms.show-assets');
    Route::put('admin/asset-placements/{placement}', [AssetController::class, 'updatePlacementCondition'])->name('assets.placement.update-condition');
    Route::delete('admin/asset-placements/{placement}', [AssetController::class, 'destroyPlacement'])->name('assets.placement.destroy');
    // Akses Tambah Placement (Bisa dipakai Guru di halaman Kelas atau Admin di halaman Ruangan)
    Route::post('assets/placement', [AssetController::class, 'storePlacement'])->name('assets.placement.store');
    Route::get('assets/placement', [AssetController::class, 'createPlacement'])->name('assets.placement.create');
});

Route::middleware(['auth', 'role:guru'])->group(function () {

    Route::get('/katrol-nilai', [GradeCurveController::class, 'index'])->name('katrol.index');
    Route::post('/katrol-nilai/process', [GradeCurveController::class, 'process'])->name('katrol.process');
    // 1. Halaman Riwayat Penilaian (Daftar penilaian yang pernah dibuat)
    Route::get('assessments', [AssessmentController::class, 'index'])->name('assessments.index');

    // 2. TAHAP 1: Membuat wadah penilaian (Pilih Kelas, Mapel, Materi)
    Route::get('assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('assessments', [AssessmentController::class, 'store'])->name('assessments.store');

    // 3. TAHAP 2: Input Skor/Nilai (Maraton input nilai siswa)
    // Rute ini membutuhkan ID Assessment yang baru saja dibuat di Tahap 1
    Route::get('assessments/{assessment}/input', [AssessmentController::class, 'input'])->name('assessments.input');

    // Simpan atau Update nilai-nilai siswa
    Route::post('assessments/{assessment}/input', [AssessmentController::class, 'updateScores'])->name('assessments.update-scores');

    // 4. Fitur Tambahan: Hapus atau Edit metadata penilaian
    Route::delete('assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');
    // LETAKKAN INI DI ATAS route resources / rute yang memakai parameter {assessment}
    Route::get('assessments/recap', [AssessmentController::class, 'recap'])->name('assessments.recap');
    Route::get('/assessments/recap-by-type', [AssessmentController::class, 'recapByType'])->name('assessments.recapByType');
    // Rute untuk Penilaian Observasi / Non-Tes
    Route::get('/observations/create', [ObservationController::class, 'create'])->name('observations.create');
    Route::post('/observations', [ObservationController::class, 'store'])->name('observations.store');
    Route::get('/observations/{assessment}/input', [ObservationController::class, 'input'])->name('observations.input');
    Route::post('/observations/{assessment}/scores', [ObservationController::class, 'updateScores'])->name('observations.updateScores');
    Route::get('/observations/{assessment}/report', [ObservationController::class, 'showReport'])->name('observations.report');
    Route::get('/capaian-pembelajaran/import', [CapaianPembelajaranController::class, 'importForm'])->name('cp.import-form');
    Route::post('/capaian-pembelajaran/import', [CapaianPembelajaranController::class, 'importProcess'])->name('cp.import-process');
    // Rute CRUD Utama (Tambahkan ->parameters)
    Route::resource('capaian-pembelajaran', CapaianPembelajaranController::class)
        ->names('cp')
        ->parameters([
            'capaian-pembelajaran' => 'cp',
        ]);
});

Route::middleware(['auth', 'role:guru'])->prefix('piket')->name('piket.')->group(function () {
    // Pengaturan Jadwal Master
    Route::get('/jadwal', [PiketController::class, 'jadwal'])->name('jadwal');
    Route::post('/jadwal', [PiketController::class, 'storeJadwal'])->name('jadwal.store');
    Route::get('/daftar-jadwal', [PiketController::class, 'daftarJadwal'])->name('daftarJadwal');

    // Pencatatan Jurnal Harian
    Route::get('/jurnal', [PiketController::class, 'jurnal'])->name('jurnal');
    Route::post('/jurnal', [PiketController::class, 'storeJurnal'])->name('jurnal.store');
    Route::get('/laporan', [PiketController::class, 'laporan'])->name('laporan');
});

Route::middleware(['auth', 'role:guru'])->prefix('jadwal-pelajaran')->name('jadwal.')->group(function () {
    Route::get('/', [JadwalPelajaranController::class, 'index'])->name('index');
    Route::post('/store', [JadwalPelajaranController::class, 'store'])->name('store');
    Route::get('/edit/{classroom}/{hari}', [JadwalPelajaranController::class, 'edit'])->name('edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/rapor/import', [GradeController::class, 'index'])->name('grades.index');
    Route::post('/rapor/import', [GradeController::class, 'import'])->name('grades.import');
    Route::get('/rapor/template', [GradeController::class, 'downloadTemplate'])->name('grades.template');
    Route::get('/rapor/rekap', [GradeController::class, 'recap'])->name('grades.recap');
    Route::get('/rapor/leger', [GradeController::class, 'ledger'])->name('grades.ledger');

    // Rute CRUD Manual
    Route::get('/exam-grades', [ExamGradeController::class, 'index'])->name('exam-grades.index');
    Route::post('/exam-grades', [ExamGradeController::class, 'store'])->name('exam-grades.store');
    Route::delete('/exam-grades/{examGrade}', [ExamGradeController::class, 'destroy'])->name('exam-grades.destroy');

    // Rute Import Excel
    Route::post('/exam-grades/import', [ExamGradeController::class, 'import'])->name('exam-grades.import');
    Route::get('/exam-grades/template', [ExamGradeController::class, 'downloadTemplate'])->name('exam-grades.template');
    Route::get('/exam-grades/input-kelas', [ExamGradeController::class, 'createBulk'])->name('exam-grades.createBulk');
    Route::post('/exam-grades/input-kelas', [ExamGradeController::class, 'storeBulk'])->name('exam-grades.storeBulk');
    Route::get('/ijazah/pengolahan', [IjazahController::class, 'index'])->name('ijazah.index');
    Route::patch('/subjects/{subject}/toggle-sidanira', [SubjectController::class, 'toggleSidanira'])->name('subjects.toggle-sidanira');
});

/*
|--------------------------------------------------------------------------
| ROUTE PROTECTED (Wajib login / middleware auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Rute e-Kinerja Saya
    Route::get('/etpp/ku', [EtppController::class, 'myEkinerja'])->name('etpp.ku');

    // Rute Kelola Bukti Dukung
    Route::post('/etpp/upload-bukti', [EtppController::class, 'uploadBukti'])->name('etpp.upload_bukti');
    Route::delete('/etpp/bukti/{id}', [EtppController::class, 'destroyBukti'])->name('etpp.destroy_bukti');
    Route::delete('/etpp/output/{output_id}/bukti', [EtppController::class, 'destroyBuktiByOutput'])->name('etpp.destroy_bukti_output');

    // Rute Import Data JSON
    Route::get('/etpp/import', [EtppController::class, 'showImportForm'])->name('etpp.import.form');
    Route::post('/etpp/import', [EtppController::class, 'importJson'])->name('etpp.import.process');

});
Route::get('/renkin', [RenkinController::class, 'index'])->name('renkin.index');

// Rute Pencarian dan Lihat e-Kinerja berdasarkan NIP
Route::post('/etpp/search', [EtppController::class, 'search'])->name('etpp.search');
Route::get('/etpp/{nip?}', [EtppController::class, 'show'])->name('etpp.show');
Route::get('/realisasi/{nip?}', [EtppController::class, 'show'])->name('etpp.show');

Route::middleware('auth')->group(function () {
    Route::get('/catatan-akhir', [StudentFinalNoteController::class, 'index'])->name('catatan_akhir.index');
    // Menampilkan halaman pembuatan catatan akhir per siswa
    Route::get('/catatan-akhir/{student_id}/{classroom_id}', [StudentFinalNoteController::class, 'edit'])->name('catatan_akhir.edit');

    // Menyimpan catatan akhir
    Route::post('/catatan-akhir/{student_id}/{classroom_id}', [StudentFinalNoteController::class, 'update'])->name('catatan_akhir.update');
    Route::get('/nilai-akhir', [FinalGradeController::class, 'index'])->name('rapor.index');
    Route::post('/nilai-akhir/fetch', [FinalGradeController::class, 'fetchRawScores'])->name('rapor.fetch');
    Route::post('/nilai-akhir/proses', [FinalGradeController::class, 'katrolNilai'])->name('rapor.process');
});

require __DIR__.'/auth.php';
