@props(['active', 'icon'])

@php
$classes = ($active ?? false)
? 'flex items-center p-3 text-white bg-indigo-600 rounded-xl transition-all duration-200 shadow-lg shadow-indigo-500/30'
: 'flex items-center p-3 text-slate-400 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all duration-200
group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
    </svg>
    <span x-show="sidebarOpen" class="ml-3 font-medium transition-opacity duration-300 whitespace-nowrap">
        {{ $slot }}
    </span>
</a>
