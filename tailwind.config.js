import defaultConfig from "./vendor/arkecosystem/foundation/resources/tailwind.config.js";
import plugin from "tailwindcss/plugin";

/** @type {import('tailwindcss').Config} */
module.exports = {
    ...defaultConfig,
    theme: {
        ...defaultConfig.theme,

        container: {
            screens: {
                // We want to exclude xs from the container max-widths
                ...defaultConfig.theme.extend.screens,
            },
        },

        extend: {
            ...defaultConfig.theme.extend,

            // Remove line-height from font sizes
            fontSize: {
                xs: '0.75rem',
                sm: '0.875rem',
                base: '1rem',
                lg: '1.125rem',
                xl: '1.25rem',
                '2xl': '1.5rem',
                '3xl': '1.875rem',
                '4xl': '2.25rem',
                '5xl': '3rem',
                '6xl': '3.75rem',
                '7xl': '4.5rem',
                '8xl': '6rem',
                '9xl': '8rem',
            },

            screens: {
                ...defaultConfig.theme.extend.screens,

                "xs": "370px",
            },

            colors: {
                ...defaultConfig.theme.extend.colors,

                "theme-orange-dark": "var(--theme-orange-dark)",
                "theme-orange-light": "var(--theme-orange-light)",

                "theme-dark-50": "var(--theme-color-dark-50)",
                "theme-dark-100": "var(--theme-color-dark-100)",
                "theme-dark-200": "var(--theme-color-dark-200)",
                "theme-dark-300": "var(--theme-color-dark-300)",
                "theme-dark-400": "var(--theme-color-dark-400)",
                "theme-dark-500": "var(--theme-color-dark-500)",
                "theme-dark-600": "var(--theme-color-dark-600)",
                "theme-dark-700": "var(--theme-color-dark-700)",
                "theme-dark-800": "var(--theme-color-dark-800)",
                "theme-dark-900": "var(--theme-color-dark-900)",
                "theme-dark-950": "var(--theme-color-dark-950)",

                "theme-dim-400": "var(--theme-color-dim-400)",
                "theme-dim-blue-600": "var(--theme-color-dim-blue-600)",
                "theme-dim-blue-700": "var(--theme-color-dim-blue-700)",

                // We don't have a dark-blue-950 so create a dim version to not confuse it
                "theme-dim-blue-950": "var(--theme-color-dim-blue-950)",

                black: "var(--theme-color-dark-950, #121213)",

                "theme-failed-state-bg": "var(--theme-failed-state-bg)",
                "theme-failed-state-text": "var(--theme-failed-state-text)",
            },

            borderRadius: {
                ...defaultConfig.theme.extend.borderRadius,

                "sm-md": "0.25rem",
            },

            borderWidth: {
                3: "3px",
                20: "20px",
            },

            backgroundSize: {
                ...defaultConfig.theme.extend.backgroundSize,
                500: "500px",
            },

            animation: {
                "move-bg": "move-bg 15s infinite linear",
                "move-bg-start-right":
                    "move-bg-start-right 15s infinite linear",
            },

            keyframes: {
                "move-bg": {
                    "0%": { backgroundPosition: 0 },
                    "100%": { backgroundPosition: "500px" },
                },
                "move-bg-start-right": {
                    "0%": { backgroundPosition: "calc(100%)" },
                    "100%": { backgroundPosition: "calc(100% + 500px)" },
                },
            },

            height: {
                ...defaultConfig.theme.extend.height,
                7: "1.75rem",
                11: "2.75rem",
                15: "3.75rem",
                30: "7.5rem",
                128: "32rem",
            },

            width: {
                ...defaultConfig.theme.extend.width,
                7: "1.75rem",
                25: "6.25rem",
                30: "7.5rem",
                41: "10.25rem",
                50: "12.5rem",
                84: "21rem",
                116: "29rem",
                164: "41rem",
                100: "25rem",
            },

            padding: {
                26: "6.5rem",
            },

            boxShadow: {
                ...defaultConfig.theme.extend.boxShadow,
                "search-subtle":
                    "0 10px 15px -3px rgba(0,0,0,.03), 0 4px 6px -2px rgba(0,0,0,.03)",
                "px": "0 0 1px 1px var(--tw-shadow-color) inset",
                "code-block": "0px 2px 3px 0px #18181B30, 0px 6px 6px 0px #18181B29, 0px 14px 8px 0px #18181B1A, 0px 25px 10px 0px #18181B08, 0px 39px 11px 0px #18181B00",
            },

            zIndex: {
                ...defaultConfig.theme.extend.zIndex,
                15: 15,
            },

            lineHeight: {
                ...defaultConfig.theme.extend.lineHeight,

                3.75: '0.9375rem', // 15px
                4.25: '1.0625rem', // 17px
                5.25: '1.3125rem', // 21px
            },

            flex: {
                2: '2 2 0%',
            },
        },
    },

    plugins: [
        ...defaultConfig.plugins,

        plugin(function ({ addVariant }) {
            addVariant("dim", ":is(.dark.dim &)");
            addVariant("dim-hover", ":is(.dark.dim &:hover)");
        }),
    ],

    content: [
        ...defaultConfig.content,

        "./resources/inertia/**/*.tsx",
    ],
};
