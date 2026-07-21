<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SIAKAD SDN Tomang 03 Pagi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700,800,600i|plus-jakarta-sans:400,500,600,700,800"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #142622;
            --pine: #1F3A33;
            --pine-light: #2C5348;
            --paper: #EEF2EA;
            --paper-2: #E4EADD;
            --card: #FFFFFF;
            --line: #D7DECB;
            --moss: #5B6A61;
            --gold: #D9A441;
            --gold-dark: #B9822A;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
        }

        .font-display {
            font-family: 'Fraunces', serif;
        }

        .seal {
            background: radial-gradient(circle at 35% 30%, var(--gold), var(--gold-dark));
            box-shadow: 0 8px 24px -8px rgba(185, 130, 42, 0.55), inset 0 0 0 3px rgba(255, 255, 255, 0.35);
        }

        .branding-panel {
            background-color: var(--ink);
            background-image: linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 100% 2.75rem;
        }

        .deck-card {
            position: absolute;
            width: 12.5rem;
            border-radius: 1.25rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(6px);
            padding: 1.15rem;
        }

        .field-input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(217, 164, 65, 0.18);
        }
    </style>
</head>

<body class="min-h-screen bg-[var(--paper)] antialiased">

    <div class="min-h-screen grid lg:grid-cols-[1fr_1.05fr]">

        <!-- Branding panel -->
        <div
            class="branding-panel hidden lg:flex flex-col justify-between text-white px-12 py-12 relative overflow-hidden">
            <a href="{{ url('/') }}" class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-full seal flex items-center justify-center text-white text-sm">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="leading-tight">
                    <div class="font-display font-semibold text-lg tracking-tight">SIAKAD</div>
                    <div class="text-[0.65rem] font-bold uppercase tracking-[0.18em] text-white/50 -mt-0.5">Tomang 03
                        Pagi</div>
                </div>
            </a>

            <div class="relative z-10 max-w-sm">
                <h1 class="font-display text-4xl font-semibold leading-[1.15] tracking-tight mb-4">
                    Portal khusus staf<br>dan tenaga pendidik.
                </h1>
                <p class="text-white/60 leading-relaxed">
                    Kelola presensi, nilai, keuangan, dan administrasi sekolah dalam satu sistem yang aman dan
                    transparan.
                </p>
            </div>

            <div class="relative h-40 z-10">
                <div class="deck-card -rotate-6 top-0 left-0">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center mb-3 text-xs">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="font-display text-sm font-semibold">Presensi</div>
                </div>
                <div class="deck-card rotate-3 top-6 left-28">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center mb-3 text-xs">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                    <div class="font-display text-sm font-semibold">Keuangan</div>
                </div>
            </div>

            <p class="text-xs text-white/40 relative z-10">&copy; {{ date('Y') }} SDN Tomang 03 Pagi</p>
        </div>

        <!-- Form panel -->
        <div class="flex items-center justify-center px-6 py-16">
            <div class="w-full max-w-sm">

                <div class="lg:hidden flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 rounded-full seal flex items-center justify-center text-white text-sm">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="leading-tight">
                        <div class="font-display font-semibold text-lg tracking-tight text-[var(--ink)]">SIAKAD</div>
                        <div class="text-[0.65rem] font-bold uppercase tracking-[0.18em] text-[var(--moss)] -mt-0.5">
                            Tomang 03 Pagi</div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="font-display text-3xl font-semibold text-[var(--ink)] tracking-tight mb-2">Masuk ke
                        Sistem</h2>
                    <p class="text-sm text-[var(--moss)]">Gunakan akun staf atau guru yang terdaftar.</p>
                </div>

                @if (session('status'))
                <div class="mb-6 px-4 py-3 rounded-xl bg-[var(--pine)]/10 text-[var(--pine)] text-sm font-semibold">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email / Username (NISN/NIP) -->
                    <div>
                        <label for="login"
                            class="block text-xs font-bold uppercase tracking-wider text-[var(--moss)] mb-2">
                            Email atau Username (NISN/NIP)
                        </label>
                        <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                            autocomplete="username"
                            class="field-input block w-full rounded-xl border border-[var(--line)] bg-[var(--card)] px-4 py-3 text-sm text-[var(--ink)] transition">
                        @error('login')
                        <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password"
                            class="block text-xs font-bold uppercase tracking-wider text-[var(--moss)] mb-2">
                            {{ __('Password') }}
                        </label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="field-input block w-full rounded-xl border border-[var(--line)] bg-[var(--card)] px-4 py-3 text-sm text-[var(--ink)] transition">
                        @error('password')
                        <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-[var(--line)] text-[var(--pine)] focus:ring-[var(--gold)]">
                            <span class="text-sm text-[var(--moss)] font-medium">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-semibold text-[var(--pine)] hover:text-[var(--gold-dark)] transition">
                            {{ __('Forgot your password?') }}
                        </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-[var(--gold)] text-[var(--ink)] rounded-xl font-bold hover:bg-[var(--gold-dark)] hover:text-white transition shadow-md shadow-[var(--gold)]/20">
                        {{ __('Log in') }}
                    </button>
                </form>

                <a href="{{ url('/') }}"
                    class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-[var(--moss)] hover:text-[var(--pine)] transition">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

</body>

</html>