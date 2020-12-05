const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/login.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/asset/css/login.scss', 'public/css')
    .copy('resources/asset/js/perfect-scrollbar.min.js','public/js/perfect-scrollbar.min.js')
    .copyDirectory('resources/asset/css/gaxon-icon/fonts','public/css/fonts')
    .copyDirectory('resources/asset/fonts/noir-pro','public/fonts/noir-pro');
