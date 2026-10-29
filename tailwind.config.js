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
                sans: ['Raleway', '"Raleway Fallback"', ...defaultTheme.fontFamily.sans],
                // "Space" theme (homepage): wide geometric display face for
                // the oversized headlines, mono for the HUD-style micro labels.
                display: ['Unbounded', '"Unbounded Fallback"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
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
                    // 200-400 are the accent steps used on the space theme;
                    // they deepen in its light mode (AA on paper), and keep
                    // these exact values everywhere else via the fallback.
                    200: 'rgb(var(--lime-200, 217 249 157) / <alpha-value>)',
                    300: 'rgb(var(--lime-300, 190 242 100) / <alpha-value>)',
                    400: 'rgb(var(--lime-400, 163 230 53) / <alpha-value>)',
                    500: '#84cc16',
                    600: '#4d7c0f',
                    700: '#3f6212',
                    800: '#365314',
                    900: '#1a2e05',
                },
                charcoal: '#1a1a1a',
                /**
                 * Deep-space surfaces for the dark homepage theme. `void` is
                 * the page background, `deep`/`panel` are the two raised
                 * steps for cards. Text on these uses white/opacity steps,
                 * accents stay on brand-300/400 (lime reads AA on near-black).
                 */
                space: {
                    void: 'rgb(var(--space-void, 5 6 10) / <alpha-value>)',
                    deep: 'rgb(var(--space-deep, 11 13 20) / <alpha-value>)',
                    panel: 'rgb(var(--space-panel, 17 20 29) / <alpha-value>)',
                },
                /**
                 * Light/dark for the space theme: `white` is the theme's
                 * foreground ("ink") and flips to near-black in light mode,
                 * so every text-white/x, border-white/x, bg-white/x inside
                 * .theme-space inverts without per-class overrides. Outside
                 * .theme-space (admin, auth) the fallback keeps it pure
                 * white. See the "Theme tokens" block in app.css.
                 */
                white: 'rgb(var(--ink, 255 255 255) / <alpha-value>)',
                /**
                 * Background system (kept deliberately tiny — two steps,
                 * used sparingly): near-invisible variation so pages don't
                 * read as flat #FFFFFF block after flat #FFFFFF block.
                 * `off` for a neutral off-white, `tint` for the rare section
                 * that wants a barely-there brand-green cast instead of
                 * gray. Both are close enough to white that swapping them
                 * in doesn't change any contrast-ratio math elsewhere.
                 */
                canvas: {
                    off: '#fafbf8',
                    tint: '#f7f9f4',
                },
            },
            boxShadow: {
                /**
                 * Tinted-charcoal replacements for Tailwind's default gray
                 * shadow-*. Both layers keep offset-y >= blur so the shadow
                 * never bleeds above an element's own top edge (same math
                 * that fixed the Portfolio/Show.vue cover-image bleed) —
                 * safe to use even when an element overlaps a dark section.
                 *
                 * Site-wide rule: `soft` is the resting elevation for cards
                 * and images; `elevated` is reserved for emphasized/lifted
                 * elements (a featured pricing tier, the nav dropdown,
                 * anything overlapping a dark section). Don't reach for
                 * stock Tailwind shadow-sm/md/lg on public pages — pick one
                 * of these two instead, so shadow weight stays meaningful.
                 *
                 * Border rule (no token needed, just convention):
                 * `border-black/10` for card/container borders,
                 * `border-gray-200`/`border-gray-100` stays reserved for
                 * plain dividers/hairlines — that's a real semantic split,
                 * not just two ways to draw the same line.
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
                        '--tw-prose-quote-borders': '#bef264',
                        a: { textDecoration: 'none', fontWeight: '500' },
                        'a:hover': { textDecoration: 'underline' },
                    },
                },
            }),
        },
    },

    plugins: [forms, typography],
};
