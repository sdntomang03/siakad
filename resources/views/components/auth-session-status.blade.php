@props(['status'])

@if ($status)
    <div role="status" {{ $attributes->merge(['class' => 'rounded-md border border-brand-200 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-900 dark:border-brand-800 dark:bg-brand-900 dark:text-brand-100']) }}>
        {{ $status }}
    </div>
@endif
