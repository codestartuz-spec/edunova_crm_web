import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Segoe UI', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Edunova asosiy rangi - qizil
                // Filial rangiga moslashadigan palitra: qiymatlar CSS o'zgaruvchilarida (resources/css/app.css, App\Support\BrandTheme)
                brand: {
                    50: 'rgb(var(--brand-50) / <alpha-value>)',
                    100: 'rgb(var(--brand-100) / <alpha-value>)',
                    200: 'rgb(var(--brand-200) / <alpha-value>)',
                    300: 'rgb(var(--brand-300) / <alpha-value>)',
                    400: 'rgb(var(--brand-400) / <alpha-value>)',
                    500: 'rgb(var(--brand-500) / <alpha-value>)',
                    600: 'rgb(var(--brand-600) / <alpha-value>)',
                    700: 'rgb(var(--brand-700) / <alpha-value>)',
                    800: 'rgb(var(--brand-800) / <alpha-value>)',
                    900: 'rgb(var(--brand-900) / <alpha-value>)',
                    950: 'rgb(var(--brand-950) / <alpha-value>)',
                },
                ink: {
                    50: '#f8f8f9',
                    100: '#f0f0f2',
                    200: '#e3e3e7',
                    300: '#c9c9d0',
                    400: '#9a9aa5',
                    500: '#6f6f7b',
                    600: '#52525e',
                    700: '#3b3b46',
                    800: '#26262e',
                    900: '#17171d',
                    950: '#0e0e12',
                },
            },
            boxShadow: {
                card: '0 1px 2px rgba(23,23,29,.04), 0 4px 16px rgba(23,23,29,.05)',
                pop: '0 10px 40px rgba(23,23,29,.15)',
            },
        },
    },
    plugins: [forms],
};
