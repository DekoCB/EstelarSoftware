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
                sans:  ['Sora', ...defaultTheme.fontFamily.sans],
                mono:  ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                estelar: {
                    bg:        '#0A0F1E',
                    card:      '#0F172A',
                    'card-2':  '#1E293B',
                    blue:      '#0EA5E9',
                    'blue-2':  '#38BDF8',
                    cyan:      '#00D4FF',
                    silver:    '#94A3B8',
                    'silver-2':'#CBD5E1',
                    success:   '#1D9E75',
                    danger:    '#D85A30',
                },
            },
            boxShadow: {
                'glow-blue': '0 0 25px rgba(14,165,233,0.45)',
                'glow-blue-lg': '0 0 50px rgba(14,165,233,0.35)',
            },
        },
    },

    plugins: [forms],
};
