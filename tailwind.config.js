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
                '7': '1.75rem',
                '11': '2.75rem',
                '30': '7.5rem',
                '128': '32rem',
            },
            width: {
                ...defaultConfig.theme.extend.width,
                '7': '1.75rem',
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

    // TODO: Move to UI package if no issues
    // Source: https://dev.to/smartmointy/tailwind-css-dark-mode-switch-with-javascript-2kl9
    variants: {
        ...defaultConfig.variants,
        textColor: ['dark', 'dark-hover', 'responsive', 'hover', 'focus'],
        backgroundColor: ['dark', 'dark-hover', 'responsive', 'hover', 'focus'],
        borderColor: ['dark', 'dark-hover', 'responsive', 'hover', 'focus'],
        boxShadow: ['dark', 'dark-hover', 'responsive', 'hover', 'focus'],
        divideColor: ['dark', 'dark-hover', 'responsive', 'hover', 'focus'],
        opacity: ['dark', 'responsive', 'hover', 'focus'],
        display: ['dark', 'responsive'],
    },
    plugins: [
        ...defaultConfig.plugins,
        plugin(function ({ addVariant, e, prefix }) {
            addVariant('dark', ({ modifySelectors, separator}) => {
                modifySelectors(({ selector }) => {
                    return selectorParser((selectors) => {
                        selectors.walkClasses((sel) => {
                            sel.value = `dark${separator}${sel.value}`
                            sel.parent.insertBefore(sel, selectorParser().astSync(prefix('.theme-dark ')))
                        })
                    }).processSync(selector);
                });
            });
            addVariant('dark-hover', ({ modifySelectors, separator}) => {
                modifySelectors(({ className }) => {
                    return `.theme-dark .${e(`dark\:hover${separator}${className}`)}:hover`;
                });
            });
        }),
    ],
    purge: {
        ...defaultConfig.purge,
        options: {
            whitelistPatterns: [
                ...defaultConfig.purge.options.whitelistPatterns,
                /^pika-/,
            ],
        },
    },
    future: {
        ...defaultConfig.future,
        defaultLineHeights: true,
    },
}
