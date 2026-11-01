<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-11 items-center justify-center rounded-md border border-transparent bg-red-700 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-red-800 active:bg-red-900 disabled:cursor-not-allowed disabled:opacity-60']) }}>
    {{ $slot }}
</button>
