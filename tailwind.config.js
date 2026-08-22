import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Raleway', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                /**
                 * 600 is the accessible workhorse (AA contrast with white text,
                 * used for buttons/links/CTAs). 400/500 are the more vivid,
                 * logo-accurate lime — for badges/accents/icons that don't need
                 * white text on top. See project memory for the contrast math.
                 */
                brand: {
                    50: '#f7fee7',
                    100: '#ecfccb',
                    200: '#d9f99d',
                    300: '#bef264',
                    400: '#a3e635',
                    500: '#84cc16',
                    600: '#4d7c0f',
                    700: '#3f6212',
                    800: '#365314',
                    900: '#1a2e05',
                },
                charcoal: '#1a1a1a',
            },
            boxShadow: {
                /**
                 * Tinted-charcoal replacements for Tailwind's default gray
                 * shadow-*. Both layers keep offset-y >= blur so the shadow
                 * never bleeds above an element's own top edge (same math
                 * that fixed the Portfolio/Show.vue cover-image bleed) —
                 * safe to use even when an element overlaps a dark section.
                 */
                soft: '0 2px 8px -2px rgb(26 26 26 / 0.08), 0 1px 3px -1px rgb(26 26 26 / 0.06)',
                elevated: '0 20px 25px -5px rgb(26 26 26 / 0.12), 0 8px 10px -6px rgb(26 26 26 / 0.08)',
            },
            keyframes: {
                blob: {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '33%': { transform: 'translate(24px, -30px) scale(1.08)' },
                    '66%': { transform: 'translate(-16px, 18px) scale(0.94)' },
                },
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                shake: {
                    '10%, 90%': { transform: 'translateX(-1px)' },
                    '20%, 80%': { transform: 'translateX(2px)' },
                    '30%, 50%, 70%': { transform: 'translateX(-4px)' },
                    '40%, 60%': { transform: 'translateX(4px)' },
                },
            },
            animation: {
                blob: 'blob 14s infinite ease-in-out',
                shake: 'shake 0.5s ease-in-out',
            },
            typography: (theme) => ({
                // Matches the CKEditor-produced content elsewhere on the
                // site (brand-green links/bullets instead of the default
                // blue, headings in the same gray as the rest of the copy).
                DEFAULT: {
                    css: {
                        '--tw-prose-body': theme('colors.gray.700'),
                        '--tw-prose-headings': theme('colors.gray.900'),
                        '--tw-prose-links': theme('colors.brand.600'),
                        '--tw-prose-bold': theme('colors.gray.900'),
                        '--tw-prose-bullets': theme('colors.brand.600'),
                        '--tw-prose-quotes': theme('colors.gray.700'),
                        '--tw-prose-quote-borders': theme('colors.brand.300'),
                        a: { textDecoration: 'none', fontWeight: '500' },
                        'a:hover': { textDecoration: 'underline' },
                    },
                },
            }),
        },
    },

    plugins: [forms, typography],
};
