<!DOCTYPE html>
@props(['wide' => false])

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistem Informasi Akademik') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f5f6f2] font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <main class="flex min-h-screen flex-col items-center justify-center px-4 py-8 sm:px-6">
            <a href="{{ url('/') }}" class="mb-6 inline-flex min-h-11 items-center gap-3 rounded-md px-2 text-brand-900 hover:text-brand-700 dark:text-brand-100 dark:hover:text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-900 text-white dark:bg-brand-700">
                    <svg aria-hidden="true" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 6.25c-1.6-1.1-3.1-1.5-5-1.5-1.4 0-2.7.3-4 1v13c1.3-.7 2.6-1 4-1 1.9 0 3.4.4 5 1.5m0-13c1.6-1.1 3.1-1.5 5-1.5 1.4 0 2.7.3 4 1v13c-1.3-.7-2.6-1-4-1-1.9 0-3.4.4-5 1.5m0-13v13" />
                    </svg>
                </span>
                <span class="text-base font-semibold">SIAKAD</span>
            </a>
            <div class="w-full {{ $wide ? 'max-w-5xl' : 'max-w-md' }} overflow-hidden rounded-lg border border-slate-200 bg-white px-5 py-6 dark:border-slate-700 dark:bg-slate-900 sm:px-8 sm:py-8">
                {{ $slot }}
            </div>
        </main>
    </body>
</html>
