let mix = require('laravel-mix');
var path = require('path');

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

mix.less('resources/assets/less/app.less', 'public/css')
    .copy('node_modules/sweetalert/dist/sweetalert.min.js', 'public/js/sweetalert.min.js')
    .copy('node_modules/sweetalert/dist/sweetalert.css', 'public/css/sweetalert.css')
    .copy('node_modules/sweetalert2/dist/sweetalert2.min.js', 'public/js/sweetalert2.min.js')
    .copy('node_modules/sweetalert2/dist/sweetalert2.css', 'public/css/sweetalert2.css')
    .js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/spa-modules/spa-projects/spa-projects.js', 'public/js/spa-modules')
    .js('resources/assets/js/spa-modules/spa-proofer/spa-proofer.js', 'public/js/spa-modules')
    .sass('resources/assets/sass/app.scss', 'public/css/spa-modules')
    .webpackConfig({
        resolve: {
            modules: [
                path.resolve(__dirname, 'vendor/laravel/spark/resources/assets/js'),
                'node_modules'
            ],
            alias: {
                'vue$': 'vue/dist/vue.js'
            }
        }
    });

mix.browserSync({
    proxy: 'localhost:8000'
});