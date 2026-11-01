<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - SIAKAD SDN Tomang 03 Pagi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f5f6f2] font-sans text-slate-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[minmax(18rem,0.8fr)_minmax(24rem,1.2fr)]">
        <aside class="hidden flex-col justify-between bg-brand-900 px-10 py-10 text-white lg:flex xl:px-16">
            <a href="{{ url('/') }}" class="inline-flex min-h-11 w-fit items-center gap-3 rounded-md text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-white/10">
                    <svg aria-hidden="true" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 6.25c-1.6-1.1-3.1-1.5-5-1.5-1.4 0-2.7.3-4 1v13c1.3-.7 2.6-1 4-1 1.9 0 3.4.4 5 1.5m0-13c1.6-1.1 3.1-1.5 5-1.5 1.4 0 2.7.3 4 1v13c-1.3-.7-2.6-1-4-1-1.9 0-3.4.4-5 1.5m0-13v13" />
                    </svg>
                </span>
                <span>
                    <span class="block text-base font-semibold">SIAKAD</span>
                    <span class="block text-sm text-brand-100">SDN Tomang 03 Pagi</span>
                </span>
            </a>

            <div class="max-w-md py-12">
                <h1 class="text-3xl font-semibold leading-tight xl:text-4xl">Sistem Informasi Akademik</h1>
                <p class="mt-4 max-w-sm text-base leading-relaxed text-brand-100">
                    Masuk untuk melanjutkan pekerjaan sekolah.
                </p>
            </div>

            <p class="text-sm text-brand-100">&copy; {{ date('Y') }} SDN Tomang 03 Pagi</p>
        </aside>

        <section class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <a href="{{ url('/') }}" class="mb-10 inline-flex min-h-11 items-center gap-3 rounded-md text-brand-900 lg:hidden">
                    <span class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-900 text-white">
                        <svg aria-hidden="true" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 6.25c-1.6-1.1-3.1-1.5-5-1.5-1.4 0-2.7.3-4 1v13c1.3-.7 2.6-1 4-1 1.9 0 3.4.4 5 1.5m0-13c1.6-1.1 3.1-1.5 5-1.5 1.4 0 2.7.3 4 1v13c-1.3-.7-2.6-1-4-1-1.9 0-3.4.4-5 1.5m0-13v13" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-base font-semibold">SIAKAD</span>
                        <span class="block text-sm text-slate-600">SDN Tomang 03 Pagi</span>
                    </span>
                </a>

                <header class="mb-8">
                    <h2 class="text-2xl font-semibold tracking-tight text-slate-950 sm:text-3xl">Masuk ke sistem</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-700">Gunakan akun staf atau guru yang terdaftar.</p>
                </header>

                @if (session('status'))
                    <div role="status" class="mb-6 rounded-md border border-brand-200 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-900">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="login" class="mb-2 block text-sm font-medium text-slate-800">
                            Email atau Username (NISN/NIP)
                        </label>
                        <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                            autocomplete="username" @if ($errors->has('login')) aria-invalid="true" aria-describedby="login-error" @endif
                            class="block min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-950 placeholder:text-slate-500 focus:border-brand-700 focus:ring-brand-700">
                        @error('login')
                            <p id="login-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-800">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            @if ($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                            class="block min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-950 focus:border-brand-700 focus:ring-brand-700">
                        @error('password')
                            <p id="password-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <label for="remember_me" class="inline-flex min-h-11 items-center gap-2 rounded-sm text-sm text-slate-800">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="h-4 w-4 rounded border-slate-400 text-brand-700 focus:ring-brand-700">
                            <span>{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="inline-flex min-h-11 items-center rounded-sm text-sm font-medium text-brand-800 underline underline-offset-4 hover:text-brand-900">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-md bg-brand-700 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-800 active:bg-brand-900">
                        {{ __('Log in') }}
                    </button>
                </form>

                <a href="{{ url('/') }}"
                    class="mt-7 inline-flex min-h-11 items-center rounded-sm text-sm font-medium text-slate-700 underline underline-offset-4 hover:text-brand-800">
                    Kembali ke beranda
                </a>
            </div>
        </section>
    </main>
</body>

</html>
