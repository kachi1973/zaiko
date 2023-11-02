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

mix
    .js('resources/js/app.js',                    'public/js')
    .js('resources/js/shukko/main.js',            'public/js/shukko.js')
    .js('resources/js/joren/main.js',             'public/js/joren.js')
    .js('resources/js/hasei/main.js',             'public/js/hasei.js')
    .js('resources/js/zido/main.js',              'public/js/zido.js')
    .js('resources/js/konyu/main.js',             'public/js/konyu.js')
    .js('resources/js/admin/zaiko/main.js',       'public/js/admin/zaiko.js')
    .js('resources/js/admin/kzaiko/main.js',      'public/js/admin/kzaiko.js')
    .js('resources/js/admin/hinmoku/main.js',      'public/js/admin/hinmoku.js')
    .js('node_modules/popper.js/dist/popper.js',  'public/js')
    .vue()
    .sass('resources/sass/app.scss',              'public/css')
    .autoload({jquery: ['$', 'window.jQuery']})
    .version()
    .sourceMaps();
mix.webpackConfig({
    stats: {
         children: true
    }
});
