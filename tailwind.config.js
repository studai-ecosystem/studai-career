/** @type {import('tailwindcss').Config} */

/*
 * MERIDIAN DESIGN SYSTEM — Tailwind configuration.
 * Every colour maps to a CSS custom property defined in resources/css/app.css,
 * so the same utilities resolve correctly in both light and dark themes.
 * Theme switching is driven by [data-theme="dark"] on <html> — never the
 * Tailwind `dark:` variant.
 */

// ── Legacy-compatibility palette ────────────────────────────────────────────
// Existing pages still reference Tailwind's default colour families
// (gray/indigo/red/…). Rather than leave those utilities colourless, every
// legacy family is aliased to MERIDIAN design tokens so all pages render in
// the MERIDIAN palette. New code must use the semantic names above instead.
const neutralScale = {
    50: 'var(--color-canvas)',
    100: 'var(--color-surface-raised)',
    200: 'var(--color-border)',
    300: 'var(--color-border-strong)',
    400: 'var(--color-ink-4)',
    500: 'var(--color-ink-3)',
    600: 'var(--color-ink-3)',
    700: 'var(--color-ink-2)',
    800: 'var(--color-ink-2)',
    900: 'var(--color-ink-1)',
    950: 'var(--color-ink-1)',
};
const accentScale = {
    50: 'var(--color-accent-subtle)',
    100: 'var(--color-accent-subtle)',
    200: 'var(--color-accent-muted)',
    300: 'var(--color-accent-muted)',
    400: 'var(--color-accent)',
    500: 'var(--color-accent)',
    600: 'var(--color-accent)',
    700: 'var(--color-accent-hover)',
    800: 'var(--color-accent-hover)',
    900: 'var(--color-accent-text)',
    950: 'var(--color-accent-text)',
};
const makeStatusScale = (base, subtle, border) => ({
    50: subtle, 100: subtle, 200: border, 300: border,
    400: base, 500: base, 600: base, 700: base, 800: base, 900: base, 950: base,
});
const successScale = makeStatusScale('var(--color-success)', 'var(--color-success-subtle)', 'var(--color-success-border)');
const warningScale = makeStatusScale('var(--color-warning)', 'var(--color-warning-subtle)', 'var(--color-warning-border)');
const errorScale = makeStatusScale('var(--color-error)', 'var(--color-error-subtle)', 'var(--color-error-border)');
const infoScale = makeStatusScale('var(--color-info)', 'var(--color-info-subtle)', 'var(--color-info-border)');
const legacyAliases = {
    gray: neutralScale, slate: neutralScale, zinc: neutralScale,
    neutral: neutralScale, stone: neutralScale,
    indigo: accentScale, blue: accentScale, sky: accentScale,
    cyan: accentScale, violet: accentScale, purple: accentScale,
    fuchsia: accentScale, pink: accentScale,
    'studai-blue': accentScale,
    green: successScale, emerald: successScale, teal: successScale, lime: successScale,
    red: errorScale, rose: errorScale,
    amber: warningScale, yellow: warningScale, orange: warningScale,
};

module.exports = {
    darkMode: ['selector', '[data-theme="dark"]'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/Livewire/**/*.php',
        './app/Filament/**/*.php',
    ],

    theme: {
        // Replace the default palette entirely — no stray Tailwind greys/indigos.
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            white: '#FFFFFF',
            black: '#000000',

            // Legacy families aliased to MERIDIAN tokens (migration bridge).
            ...legacyAliases,

            canvas: 'var(--color-canvas)',
            surface: {
                DEFAULT: 'var(--color-surface)',
                raised: 'var(--color-surface-raised)',
                sunken: 'var(--color-surface-sunken)',
            },

            ink: {
                1: 'var(--color-ink-1)',
                2: 'var(--color-ink-2)',
                3: 'var(--color-ink-3)',
                4: 'var(--color-ink-4)',
                inverse: 'var(--color-ink-inverse)',
            },

            border: {
                DEFAULT: 'var(--color-border)',
                strong: 'var(--color-border-strong)',
                inverse: 'var(--color-border-inverse)',
            },

            accent: {
                DEFAULT: 'var(--color-accent)',
                hover: 'var(--color-accent-hover)',
                subtle: 'var(--color-accent-subtle)',
                muted: 'var(--color-accent-muted)',
                text: 'var(--color-accent-text)',
            },

            success: {
                DEFAULT: 'var(--color-success)',
                subtle: 'var(--color-success-subtle)',
                border: 'var(--color-success-border)',
            },
            warning: {
                DEFAULT: 'var(--color-warning)',
                subtle: 'var(--color-warning-subtle)',
                border: 'var(--color-warning-border)',
            },
            error: {
                DEFAULT: 'var(--color-error)',
                subtle: 'var(--color-error-subtle)',
                border: 'var(--color-error-border)',
            },
            info: {
                DEFAULT: 'var(--color-info)',
                subtle: 'var(--color-info-subtle)',
                border: 'var(--color-info-border)',
            },

            // Charts and data-viz only — never for UI chrome.
            data: {
                1: 'var(--color-data-1)',
                2: 'var(--color-data-2)',
                3: 'var(--color-data-3)',
                4: 'var(--color-data-4)',
                5: 'var(--color-data-5)',
                6: 'var(--color-data-6)',
            },
        },

        fontFamily: {
            display: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            ui: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            mono: ['Roboto Mono', 'ui-monospace', 'monospace'],
        },

        fontSize: {
            11: ['0.6875rem', { lineHeight: '1rem' }],
            12: ['0.75rem', { lineHeight: '1.125rem' }],
            14: ['0.875rem', { lineHeight: '1.5' }],
            16: ['1rem', { lineHeight: '1.5' }],
            18: ['1.125rem', { lineHeight: '1.5' }],
            24: ['1.5rem', { lineHeight: '1.3' }],
            32: ['2rem', { lineHeight: '1.2' }],
            48: ['3rem', { lineHeight: '1.1' }],
            64: ['4rem', { lineHeight: '1.05' }],
            96: ['6rem', { lineHeight: '1' }],
        },

        borderRadius: {
            none: '0',
            sm: '4px',
            DEFAULT: '8px',
            md: '8px',
            lg: '12px',
            xl: '16px',
            // Legacy oversized radii capped at MERIDIAN max (16px) so old
            // rounded-2xl / rounded-3xl markup reads intentional, not sharp.
            '2xl': '16px',
            '3xl': '16px',
            full: '9999px', // reserved for avatars/pills/tags only — never buttons
        },

        // MERIDIAN has no gradients. Wipe Tailwind's gradient-direction
        // utilities so every legacy `bg-gradient-to-*` renders flat.
        backgroundImage: {
            none: 'none',
        },

        extend: {
            // 4px base spacing scale.
            spacing: {
                1: '4px',
                2: '8px',
                3: '12px',
                4: '16px',
                5: '20px',
                6: '24px',
                8: '32px',
                10: '40px',
                12: '48px',
                16: '64px',
                24: '96px',
                sidebar: 'var(--sidebar-width)',
                topbar: 'var(--topbar-height)',
            },
            maxWidth: {
                content: 'var(--content-max-width)',
                reading: 'var(--reading-max-width)',
            },
            // Shadows exist only for overlays (modals, popovers, toasts).
            // Every default Tailwind shadow utility is forced flat so the
            // ~1300 legacy `shadow-sm/md/lg/xl/2xl` usages render no shadow.
            boxShadow: {
                overlay: '0 16px 48px -12px rgba(0, 0, 0, 0.24)',
                popover: '0 8px 24px -8px rgba(0, 0, 0, 0.18)',
                none: 'none',
                sm: 'none',
                DEFAULT: 'none',
                md: 'none',
                lg: 'none',
                xl: 'none',
                '2xl': 'none',
                inner: 'none',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
    ],
};
