@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'min-h-11 rounded-md border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-brand-700 focus:ring-brand-700 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-400 dark:disabled:bg-slate-800']) }}>
