@props(['active', 'icon'])

@php
$classes = ($active ?? false)
? 'flex min-h-11 items-center rounded-md bg-brand-700 px-3 py-2.5 text-white transition-colors duration-150'
: 'group flex min-h-11 items-center rounded-md px-3 py-2.5 text-slate-200/85 transition-colors duration-150 hover:bg-white/10 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}
    @if ($active ?? false) aria-current="page" @endif
    aria-label="{{ trim(strip_tags($slot)) }}"
    title="{{ trim(strip_tags($slot)) }}">
    <svg aria-hidden="true" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
    </svg>
    <span x-show="sidebarOpen" class="ml-3 truncate text-sm font-medium">
        {{ $slot }}
    </span>
</a>
