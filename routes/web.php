<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KelulusanController;
use App\Http\Controllers\Operator\UserController as OperatorUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

    Route::get('teacher-notes', [TeacherNoteController::class, 'index'])->name('teacher-notes.index');
    Route::post('teacher-notes', [TeacherNoteController::class, 'store'])->name('teacher-notes.store');
    Route::delete('teacher-notes/{id}', [TeacherNoteController::class, 'destroy'])->name('teacher-notes.destroy');
    Route::get('teacher-notes/report', [TeacherNoteController::class, 'report'])->name('teacher-notes.report');
});

Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {

    // Rute Reset Password
    Route::patch('users/{user}/reset-password', [OperatorUserController::class, 'resetPassword'])
        ->name('users.reset-password')
        ->middleware('permission:edit-users');

    // Rute CRUD User Guru
    Route::resource('users', OperatorUserController::class)->except(['create', 'edit', 'show']);

});

Route::middleware(['auth', 'role:superadmin|operator'])->group(function () {
    Route::resource('subjects', SubjectController::class)->except(['create', 'show', 'edit']);
    Route::get('kelulusan/import', [KelulusanController::class, 'showImportForm'])->name('kelulusan.import');
    Route::post('kelulusan/import', [KelulusanController::class, 'importExcel'])->name('kelulusan.import.process');
    Route::delete('/dashboard/kelulusan/kosongkan', [KelulusanController::class, 'deleteAll'])->name('kelulusan.delete-all');
    Route::post('kelulusan/{id}/update-status', [KelulusanController::class, 'updateStatusAjax'])->name('kelulusan.update_status');
});

Route::middleware(['auth', 'role:guru'])->group(function () {

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
});
require __DIR__.'/auth.php';
