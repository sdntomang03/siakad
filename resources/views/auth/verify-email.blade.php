<x-guest-layout>
    <div class="mb-6 text-sm leading-6 text-slate-700 dark:text-slate-300">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div role="status" class="mb-5 rounded-md border border-brand-200 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-900 dark:border-brand-800 dark:bg-brand-900 dark:text-brand-100">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="min-h-11 rounded-sm text-sm font-medium text-slate-700 underline underline-offset-4 hover:text-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 dark:text-slate-300 dark:hover:text-white">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
