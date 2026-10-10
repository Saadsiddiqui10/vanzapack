import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/**/*.php',
    ],

    safelist: [
        'text-brand-600', 'text-brand-800', 'text-amber-600', 'text-rose-600', 'text-sky-600',
        'bg-brand-100', 'bg-amber-100', 'bg-sky-100', 'bg-indigo-100', 'bg-rose-100', 'bg-slate-100',
        'text-amber-800', 'text-sky-800', 'text-indigo-800', 'text-rose-700', 'text-slate-700',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f3f9e8',
                    100: '#e6f2cf',
                    200: '#d0e7a6',
                    300: '#b8d977',
                    400: '#a4cf52',
                    500: '#95c93f', // primary
                    600: '#7aa82f',
                    700: '#5e8226',
                    800: '#4b6622',
                    900: '#3f5620',
                },
                navy: {
                    50: '#e8e8f3',
                    100: '#c7c7e2',
                    200: '#9a9acf',
                    300: '#6c6cbb',
                    400: '#3d3da3',
                    500: '#1a1a86',
                    600: '#000066', // secondary
                    700: '#00004f',
                    800: '#00003a',
                    900: '#000026',
                },
            },
            // Green *text* uses the logo's forest green (#063d2a). Backgrounds/buttons keep
            // the brand scale above; light shades (50–400, used on dark sections) are unchanged.
            textColor: {
                brand: {
                    500: '#063d2a',
                    600: '#0b5a3e', // hover: a touch lighter
                    700: '#063d2a',
                    800: '#063d2a',
                    900: '#042a1d',
                },
            },
            boxShadow: {
                card: '0 1px 2px rgba(16,24,40,.06), 0 1px 3px rgba(16,24,40,.1)',
                'card-hover': '0 12px 32px -8px rgba(0,0,102,.18)',
            },
            borderRadius: {
                xl: '0.875rem',
                '2xl': '1.25rem',
            },
            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: 0, transform: 'translateY(8px)' },
                    '100%': { opacity: 1, transform: 'translateY(0)' },
                },
                'pop': {
                    '0%': { transform: 'scale(.8)' },
                    '50%': { transform: 'scale(1.15)' },
                    '100%': { transform: 'scale(1)' },
                },
            },
            animation: {
                'fade-in-up': 'fade-in-up .3s ease-out both',
                pop: 'pop .3s ease-out',
            },
        },
    },

    plugins: [forms],
};
