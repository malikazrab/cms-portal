import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'slate': {
                    '50': '#f8fafc',
                    '100': '#f1f5f9',
                    '200': '#e2e8f0',
                    '300': '#cbd5e1',
                    '400': '#94a3b8',
                    '500': '#64748b',
                    '600': '#475569',
                    '700': '#334155',
                    '800': '#1e293b',
                    '900': '#0f172a',
                },
                'stone': {
                    '50': '#fafaf9',
                    '100': '#f5f5f4',
                    '200': '#e7e5e4',
                    '300': '#d6d3d1',
                    '400': '#a8a29e',
                    '500': '#78716c',
                    '600': '#57534e',
                    '700': '#45403f',
                    '800': '#35302f',
                    '900': '#292524',
                },
            },
        },
    },

    plugins: [forms],
};

