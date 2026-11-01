@props(['label', 'value'])

<div>
    <dt class="mb-1 text-xs font-medium text-slate-600 dark:text-slate-300">{{ $label }}</dt>
    <dd class="break-words text-sm font-semibold text-slate-950 dark:text-slate-100">{{ $value }}</dd>
</div>