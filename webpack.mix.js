const mix = require('laravel-mix');

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

mix
    // Options
    .options({
        processCssUrls: false,
    })
    // App
    .js('resources/js/app.js', 'public/js')
    .copy('resources/js/chart.js', 'public/js/chart.js')
    .copy('resources/js/vendor/ark/clipboard.js', 'public/js/clipboard.js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ])
    // .copy('resources/js/vendor/ark/clipboard.js', 'public/js/clipboard.js')
    // .copy('node_modules/swiper/swiper-bundle.min.js', 'public/js/swiper.js')
    .copyDirectory('resources/images', 'public/images')
    // Fonts
    .copyDirectory('resources/fonts', 'public/fonts')
    // Extract node_modules
    .extract(['alpinejs', 'chart.js']);

if (mix.inProduction()) {
    mix.version();
}
