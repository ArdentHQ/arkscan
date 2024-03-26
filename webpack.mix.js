const mix = require("laravel-mix");
const focusVisible = require("postcss-focus-visible");
const path = require("path");

mix.webpackConfig({
    resolve: {
        alias: {
            "@ui": path.resolve(
                __dirname,
                "vendor/arkecosystem/foundation/resources/assets/"
            ),
        },
    },
    // @see: https://laravel-mix.com/docs/6.0/upgrade#unused-library-extraction
    optimization: {
        providedExports: false,
        sideEffects: false,
        usedExports: false,
    },
})
    // Options
    .options({
        processCssUrls: false,
    })
    // App
    .js("resources/js/app.js", "public/js")
    .js("resources/js/chart-tooltip.js", "public/js")
    .postCss("resources/css/app.css", "public/css", [
        require("postcss-import"),
        require("tailwindcss"),
        focusVisible(),
    ])
    .copyDirectory("resources/images", "public/images")
    // Extract node_modules
    .extract(["alpinejs", "chart.js"]);

if (process.env.MIX_NOTIFICATIONS_DISABLED === "true") {
    mix.disableSuccessNotifications();
}

if (mix.inProduction()) {
    mix.version();
}
