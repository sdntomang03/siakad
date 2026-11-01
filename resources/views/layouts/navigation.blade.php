<nav x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false"
    aria-label="Navigasi akun"
    class="sticky top-0 z-30 border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 md:z-40">
    <div class="mx-auto flex h-16 max-w-screen-2xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" type="button"
                :aria-expanded="sidebarOpen.toString()" aria-controls="main-sidebar"
                :aria-label="sidebarOpen ? 'Tutup navigasi' : 'Buka navigasi'"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-md text-brand-900 hover:bg-brand-50 focus-visible:outline-offset-2 dark:text-slate-100 dark:hover:bg-slate-800 md:hidden">
                <svg x-show="!sidebarOpen" aria-hidden="true" class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="sidebarOpen" aria-hidden="true" class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24"
                    style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <span class="truncate text-sm font-semibold text-brand-900 dark:text-slate-100 md:hidden">SIAKAD</span>
        </div>

        <div class="hidden items-center sm:flex">
            <x-dropdown align="right" width="56" contentClasses="py-1 bg-white dark:bg-slate-800">
                <x-slot name="trigger">
                    <button type="button" aria-label="Menu pengguna" :aria-expanded="open.toString()"
                        class="inline-flex min-h-11 items-center gap-3 rounded-md px-2.5 text-left text-sm font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                        <span aria-hidden="true"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-brand-100 font-semibold text-brand-900 dark:bg-brand-800 dark:text-white">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </span>
                        <span class="max-w-48 truncate">{{ Auth::user()->name }}</span>
                        <svg aria-hidden="true" class="h-4 w-4 shrink-0 fill-current"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="border-b border-slate-200 px-4 py-3 text-xs text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        Masuk sebagai
                        <span class="mt-1 block font-semibold text-brand-800 dark:text-brand-200">
                            {{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}
                        </span>
                    </div>

                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profil Saya') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-red-700 hover:bg-red-50 hover:text-red-800 dark:text-red-300 dark:hover:bg-red-950">
                            {{ __('Keluar / Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <button @click="open = !open" type="button" aria-controls="mobile-user-menu"
            :aria-expanded="open.toString()" :aria-label="open ? 'Tutup menu pengguna' : 'Buka menu pengguna'"
            class="inline-flex min-h-11 items-center gap-2 rounded-md px-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800 sm:hidden">
            <span aria-hidden="true"
                class="flex h-8 w-8 items-center justify-center rounded-md bg-brand-100 font-semibold text-brand-900 dark:bg-brand-800 dark:text-white">
                {{ substr(Auth::user()->name, 0, 1) }}
            </span>
            <svg aria-hidden="true" :class="open ? 'rotate-180' : ''"
                class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
            </svg>
        </button>
    </div>

    <div id="mobile-user-menu" x-show="open" x-transition
        class="border-t border-slate-200 bg-white px-4 pb-4 pt-3 dark:border-slate-700 dark:bg-slate-900 sm:hidden"
        style="display: none;">
        <div class="mb-3">
            <div class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ Auth::user()->name }}</div>
            <div class="truncate text-sm text-slate-600 dark:text-slate-300">{{ Auth::user()->email }}</div>
        </div>

        <div class="space-y-1">
            <x-responsive-nav-link :href="route('profile.edit')">
                {{ __('Profil Saya') }}
            </x-responsive-nav-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Keluar') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
