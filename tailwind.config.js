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
                sans: ['"Plus Jakarta Sans Variable"', 'Inter', 'Segoe UI', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Asosiy rang: binafsha-indigo. Filial rangiga moslashadi: qiymatlar CSS o'zgaruvchilarida
                // (resources/css/app.css, App\Support\BrandTheme)
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
                // Iliq aksent (shaftoli-marjon): e'tibor jalb qiladigan kichik detallar uchun
                accent: {
                    50: '#fff4ef',
                    100: '#ffe6db',
                    200: '#ffcab4',
                    300: '#ffa584',
                    400: '#ff8159',
                    500: '#fb5f33',
                    600: '#e9441a',
                    700: '#c23414',
                },
                // Xato, qarz, "o'chirish" ma'nolari uchun. Asosiy rang o'zgarsa ham xato doim qizil qoladi
                danger: {
                    50: '#fff1f2',
                    100: '#ffe1e4',
                    200: '#ffc8ce',
                    300: '#ff9aa6',
                    400: '#f86677',
                    500: '#ec3a52',
                    600: '#d8213d',
                    700: '#b4162f',
                    800: '#93152c',
                    900: '#7a162b',
                    950: '#430811',
                },
                // Neytral ranglar: ozgina binafsha tusli
                ink: {
                    50: '#f8f7fc',
                    100: '#f0eff7',
                    200: '#e3e1ef',
                    300: '#c8c6da',
                    400: '#9896b0',
                    500: '#6e6c88',
                    600: '#535169',
                    700: '#3c3a52',
                    800: '#27263c',
                    900: '#181729',
                    950: '#0f0e1d',
                },
            },
            borderRadius: {
                '2xl': '1.1rem',
                '3xl': '1.5rem',
            },
            boxShadow: {
                card: '0 1px 2px rgba(24,23,41,.04), 0 6px 20px -6px rgba(60,45,150,.10)',
                pop: '0 14px 44px -8px rgba(24,23,41,.28)',
                glow: '0 10px 30px -10px rgb(var(--brand-600) / .55)',
            },
            keyframes: {
                float: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-8px)' } },
            },
            animation: {
                float: 'float 6s ease-in-out infinite',
            },
        },
    },
    plugins: [forms],
};
