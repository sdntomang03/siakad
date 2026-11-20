import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        'bg-amber-50',
        'bg-amber-100',
        'bg-amber-200/80',
        'border',
        'border-amber-200',
        'border-amber-200/70',
        'border-b',
        'font-bold',
        'font-semibold',
        'flex',
        'gap-3',
        'h-7',
        'inline-flex',
        'items-center',
        'items-start',
        'justify-between',
        'justify-center',
        'leading-relaxed',
        'mt-0.5',
        'mt-1',
        'mt-4',
        'p-5',
        'pb-3',
        'px-3',
        'py-1',
        'rounded-2xl',
        'rounded-full',
        'shadow-sm',
        'shrink-0',
        'text-amber-700',
        'text-amber-800',
        'text-amber-900',
        'text-lg',
        'text-slate-900',
        'text-sm',
        'text-xs',
        'tracking-wider',
        'uppercase',
        'w-7',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f1f7f3',
                    100: '#dcebe3',
                    200: '#bdd8ca',
                    300: '#91bda7',
                    400: '#639c82',
                    500: '#42836e',
                    600: '#2f725d',
                    700: '#255b4b',
                    800: '#1d4b3e',
                    900: '#173c32',
                },
                accent: '#8a5e13',
            },
        },
    },

    plugins: [forms],
};
