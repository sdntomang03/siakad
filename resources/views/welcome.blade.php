@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-600 to-purple-700">
    <!-- Navigation -->
    <nav class="bg-white bg-opacity-10 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-white text-2xl font-bold">SIAKAD</div>
                <div class="space-x-4">
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Logout</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}"
                        class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Login</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="bg-white text-blue-600 px-4 py-2 rounded font-semibold hover:bg-opacity-90">Register</a>
                    @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-5xl sm:text-6xl font-bold text-white mb-6">
                Sistem Informasi Akademik
            </h1>
            <p class="text-xl text-white text-opacity-90 mb-8">
                Platform lengkap untuk mengelola data akademik secara efisien dan terintegrasi
            </p>
            @guest
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}"
                    class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-opacity-90 transition">
                    Masuk
                </a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:bg-opacity-10 transition">
                    Daftar
                </a>
                @endif
            </div>
            @endguest
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div
                class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-8 text-white hover:bg-opacity-20 transition">
                <div class="text-4xl mb-4">📚</div>
                <h3 class="text-xl font-bold mb-4">Manajemen Akademik</h3>
                <p class="text-white text-opacity-90">
                    Kelola mata pelajaran, jadwal, dan nilai siswa dengan mudah dan terstruktur
                </p>
            </div>

            <!-- Feature 2 -->
            <div
                class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-8 text-white hover:bg-opacity-20 transition">
                <div class="text-4xl mb-4">👥</div>
                <h3 class="text-xl font-bold mb-4">Data Siswa & Guru</h3>
                <p class="text-white text-opacity-90">
                    Kelola profil lengkap siswa, guru, dan staf dengan sistem database terpusat
                </p>
            </div>

            <!-- Feature 3 -->
            <div
                class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-8 text-white hover:bg-opacity-20 transition">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-xl font-bold mb-4">Laporan & Analitik</h3>
                <p class="text-white text-opacity-90">
                    Buat laporan komprehensif dan analitik untuk pengambilan keputusan yang tepat
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black bg-opacity-20 text-white py-8 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2024 SIAKAD. All rights reserved.</p>
        </div>
    </footer>
</div>
@endsection