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
            // --- CONTOH WARNA UNGU FIKTIF ---
            // ANDA HARUS MENGGANTI INI DENGAN WARNA YANG ANDA INGINKAN
            colors: {
                'brand-purple-page-bg': '#2D1B3F',      // Ungu sangat gelap untuk latar halaman
                'brand-purple-deep': '#1A0F24',         // Ungu paling gelap untuk gradient
                'brand-purple-mid-dark': '#4A2A6C',     // Ungu gelap tengah untuk gradient
                'brand-purple-mid-light': '#7B4C9F',    // Ungu terang tengah untuk gradient & teks tombol
                'brand-purple-hero-text-subtle': '#BEA8D3', // Ungu pucat untuk teks paragraf
                'brand-purple-mountain-far': '#3E2F5B', // Ungu abu-abu untuk gunung jauh
                'brand-purple-mountain-mid': '#5C3F8B', // Ungu lebih jelas untuk gunung tengah
                'brand-purple-forest-front': '#392350', // Ungu gelap untuk hutan depan
                'brand-purple-trees-detail': '#251733', // Ungu sangat gelap untuk detail pohon
            },

            fontFamily: {
                sans: ['Instrument Sans', ...defaultTheme.fontFamily.sans],
            },

            animation: {
                twinkle: 'twinkle-animation 1.5s infinite alternate',
            },
            keyframes: {
                'twinkle-animation': {
                    '0%': { opacity: '0.3' },
                    '100%': { opacity: '1' },
                }
            }
        },
    },

    plugins: [
        forms,
    ],
};