<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query
        $query = User::with(['school', 'roles'])->latest();

        // 1. Fitur Pencarian (Berdasarkan Nama atau Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 2. Filter berdasarkan Sekolah
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // 3. Filter berdasarkan Role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // 4. Penyesuaian Data per Halaman (Default 10)
        $perPage = $request->input('per_page', 10);

        // appends(request()->query()) sangat penting agar saat pindah halaman, filter tidak hilang
        $users = $query->paginate($perPage)->appends($request->query());

        $schools = School::all();
        $roles = Role::all();

        return view('superadmin.users.index', compact('users', 'schools', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'school_id' => 'nullable|exists:schools,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'school_id' => $validated['school_id'],
            'password' => Hash::make('12345678'), // Password default
        ]);

        $user->assignRole($request->role);

        return back()->with('success', 'User '.$user->name.' berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'school_id' => 'nullable|exists:schools,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'school_id' => $validated['school_id'],
        ]);

        // Mengganti role lama dengan role baru
        $user->syncRoles($request->role);

        return back()->with('success', 'Data '.$user->name.' berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('12345678')]);

        return back()->with('success', 'Password reset menjadi 12345678');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
