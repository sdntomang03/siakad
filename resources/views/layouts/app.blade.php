<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
        // Cek localStorage dulu. Jika kosong (kunjungan pertama), gunakan deteksi layar (Desktop=True, HP=False)
        sidebarOpen: localStorage.getItem('sidebarOpen') !== null
                     ? localStorage.getItem('sidebarOpen') === 'true'
                     : window.innerWidth >= 768
    }" x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIAKAD') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    <div class="flex h-screen overflow-hidden">

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden">

            @include('layouts.navigation')

            @isset($header)
            <header class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
    <x-sweet-alert />
</body>

</html>