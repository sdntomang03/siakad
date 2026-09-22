<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'sometimes|required_without:email',
            'email' => 'sometimes|required_without:login|email',
            'username' => 'sometimes|required_without:login|string',
            'password' => 'required',
        ]);

        $loginIdentifier = $request->input('login') ?? $request->input('email') ?? $request->input('username');

        // Cek apakah input berupa email atau username (NISN/NIP)
        $loginField = filter_var($loginIdentifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $loginIdentifier)->first();

        // Jika user tidak ditemukan atau password salah
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial tidak valid.',
            ], 401);
        }

        // Hapus token lama agar tidak menumpuk (Opsional)
        $user->tokens()->delete();

        // Buat Token API baru untuk Mobile
        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login Berhasil',
            'data' => [
                'user' => $user,
                'role' => $user->getRoleNames()->first(), // Mengambil role dari Spatie
                'token' => $token,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout Berhasil',
        ]);
    }
}
