const mix = require('laravel-mix');
const focusVisible = require('postcss-focus-visible');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
        resolve: {
            alias: {
                '@ui': path.resolve(__dirname, 'vendor/arkecosystem/ui/resources/assets/')
            }
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
