import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
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
