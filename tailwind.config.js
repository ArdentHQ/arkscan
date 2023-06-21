const defaultConfig = require("./vendor/arkecosystem/foundation/resources/tailwind.config.js");
const plugin = require("tailwindcss/plugin");
const selectorParser = require("postcss-selector-parser");

/** @type {import('tailwindcss').Config} */
module.exports = {
    ...defaultConfig,
    theme: {
        ...defaultConfig.theme,
        extend: {
            ...defaultConfig.theme.extend,

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

                black: "var(--theme-color-dark-950)",
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
                50: "12.5rem",
                84: "21rem",
                116: "29rem",
                164: "41rem",
            },

            padding: {
                26: "6.5rem",
            },

            boxShadow: {
                ...defaultConfig.theme.extend.boxShadow,
                "search-subtle":
                    "0 10px 15px -3px rgba(0,0,0,.03), 0 4px 6px -2px rgba(0,0,0,.03)",
            },

            zIndex: {
                ...defaultConfig.theme.extend.zIndex,
                15: 15,
            },
        },
    },
};
