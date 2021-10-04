const mix = require('laravel-mix');
const focusVisible = require('postcss-focus-visible');
const path = require('path');

mix.webpackConfig({
        resolve: {
            alias: {
                '@ui': path.resolve(__dirname, 'vendor/arkecosystem/ui/resources/assets/')
            }
        },
        // @see: https://laravel-mix.com/docs/6.0/upgrade#unused-library-extraction
        optimization: {
            providedExports: false,
            sideEffects: false,
            usedExports: false
        }
    })
    // Options
    .options({
        processCssUrls: false,
    })
    // App
    .js('resources/js/app.js', 'public/js')
    .copy('vendor/arkecosystem/ui/resources/assets/js/clipboard.js', 'public/js/clipboard.js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss')(),
        focusVisible()
    ])
    .copyDirectory('resources/images', 'public/images')
    // Extract node_modules
    .extract(['alpinejs', 'chart.js']);

if (mix.inProduction()) {
    mix.version();
}
