<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-users')->only('index');
        $this->middleware('permission:create-users')->only('store');
        $this->middleware('permission:edit-users')->only(['update', 'resetPassword']);
        $this->middleware('permission:delete-users')->only('destroy');
    }

    public function index(Request $request)
    {
        $operator = auth()->user();

        // Load relasi employee agar bisa ditampilkan di modal
        $query = User::with(['roles', 'employee'])
            ->where('school_id', $operator->school_id)
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->paginate(10)->appends($request->query());
        $roles = Role::whereIn('name', ['kepsek', 'guru', 'siswa'])->get();

        return view('operator.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $operator = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,name|in:kepsek,guru,siswa',
            // Validasi tambahan jika bukan siswa
            'jenis_kelamin' => 'required_unless:role,siswa|in:L,P',
            'nip' => 'nullable|string',
        ]);

        // 1. Buat Akun Login
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'school_id' => $operator->school_id,
            'password' => Hash::make('12345678'),
        ]);

        $user->assignRole($request->role);

        // 2. Jika bukan siswa, otomatis buatkan profil Employee
        if (in_array($request->role, ['guru', 'kepsek'])) {
            $user->employee()->create([
                'school_id' => $operator->school_id,
                'nama_lengkap' => $request->name,
                'kategori_pegawai' => $request->role,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nip' => $request->nip,
            ]);
        }
        // 3. Jika Siswa, buatkan kerangka dasar Student (Opsional, tergantung alur Anda nanti)
        elseif ($request->role === 'siswa') {
            // Logika pembuatan tabel student, family, health dll bisa diletakkan di sini
            // atau di StudentController terpisah.
        }

        return back()->with('success', 'Akun '.$user->name.' berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $operator = auth()->user();

        if ($user->school_id !== $operator->school_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|exists:roles,name|in:kepsek,guru,siswa',
            'jenis_kelamin' => 'required_unless:role,siswa|in:L,P',
            'nip' => 'nullable|string',
        ]);

        // Update User
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        $user->syncRoles($request->role);

        // Update Employee (Hanya jika bukan siswa)
        if (in_array($request->role, ['guru', 'kepsek'])) {
            $user->employee()->updateOrCreate(
                ['user_id' => $user->id], // Cari berdasarkan user_id
                [
                    'school_id' => $operator->school_id,
                    'nama_lengkap' => $request->name,
                    'kategori_pegawai' => $request->role,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'nip' => $request->nip,
                ]
            );
        }

        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        if ($user->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $user->update(['password' => Hash::make('12345678')]);

        return back()->with('success', 'Password pengguna berhasil direset ke 12345678');
    }

    public function destroy(User $user)
    {
        // Cegah hapus diri sendiri & cegah hapus user sekolah lain
        if ($user->id === auth()->id() || $user->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
