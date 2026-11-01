@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block min-h-11 w-full rounded-md bg-brand-50 px-3 py-2 text-start text-sm font-semibold text-brand-900 transition-colors dark:bg-brand-900 dark:text-brand-100'
            : 'block min-h-11 w-full rounded-md px-3 py-2 text-start text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 hover:text-slate-950 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @if ($active ?? false) aria-current="page" @endif>
    {{ $slot }}
</a>
