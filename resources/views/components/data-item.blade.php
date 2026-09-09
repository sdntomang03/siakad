@props(['label', 'value'])

<div>
    <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ $label }}</dt>
    <dd class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $value }}</dd>
</div>