const defaultConfig = require('./vendor/arkecosystem/ui/resources/tailwind.config.js');
const plugin = require("tailwindcss/plugin");
const selectorParser = require("postcss-selector-parser");

module.exports = {
    ...defaultConfig,
    theme: {
        ...defaultConfig.theme,
        extend: {
            ...defaultConfig.theme.extend,
            borderWidth: {
                3: '3px',
                20: '20px',
            },
            height: {
                ...defaultConfig.theme.extend.height,
                '7': '1.75rem',
                '11': '2.75rem',
                '30': '7.5rem',
                '128': '32rem',
            },
            width: {
                ...defaultConfig.theme.extend.width,
                '7': '1.75rem',
                '25': '6.25rem',
                '30': '7.5rem',
                '84': '21rem',
            },
            padding: {
                '26': '6.5rem',
            },
            boxShadow: {
                ...defaultConfig.theme.extend.boxShadow,
                "search-subtle": "0 10px 15px -3px rgba(0,0,0,.03), 0 4px 6px -2px rgba(0,0,0,.03)",
            }
        },
    },
    variants: {
        ...defaultConfig.variants,
        extend: {
            ...defaultConfig.variants.extend,
            borderRadius: ['focus-visible'],
            ringColor: ['focus-visible'],
            ringWidth: ['focus-visible'],
            textDecoration: ['focus-visible'],
            transitionProperty: ['focus-visible'],
            zIndex: ['focus-visible'],
        },
    },
}
