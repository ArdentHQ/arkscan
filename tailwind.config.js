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

            screens: {
                ...defaultConfig.theme.extend.screens,
                'md-lg': '960px',
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
                "toggle-dropdown":
                    "0px 2px 6px rgba(33, 34, 37, 0.06), 0px 32px 41px -23px rgba(33, 34, 37, 0.07)",
            },

            zIndex: {
                ...defaultConfig.theme.extend.zIndex,
                15: 15,
            },
        },
    },
};
