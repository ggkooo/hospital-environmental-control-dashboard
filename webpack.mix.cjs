// webpack.mix.cjs
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')

    // SIDEBAR
    .js('resources/js/layout/sidebar.js', 'public/js/layout')
    .sass('resources/sass/layout/sidebar.scss', 'public/css/layout')
    
    // HEADER
    .js('resources/js/layout/header.js', 'public/js/layout')
    .sass('resources/sass/layout/header.scss', 'public/css/layout');
