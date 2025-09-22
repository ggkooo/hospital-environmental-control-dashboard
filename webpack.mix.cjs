// webpack.mix.cjs
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')

    // SIDEBAR
    .js('resources/js/layout/sidebar.js', 'public/js/layout')
    .sass('resources/sass/layout/sidebar.scss', 'public/css/layout')

    // HEADER
    .js('resources/js/layout/header.js', 'public/js/layout')
    .sass('resources/sass/layout/header.scss', 'public/css/layout')

    // FOOTER
    .js('resources/js/layout/footer.js', 'public/js/layout')
    .sass('resources/sass/layout/footer.scss', 'public/css/layout')

    // HOME
    //.js('resources/js/home/home.js', 'public/js/home')
    .sass('resources/sass/pages/home.scss', 'public/css/pages')

    // TEMPERATURE
    .js('resources/js/pages/temperature.js', 'public/js/pages')
    .sass('resources/sass/pages/temperature.scss', 'public/css/pages')

    // HUMIDITY
    .js('resources/js/pages/humidity.js', 'public/js/pages')
    .sass('resources/sass/pages/humidity.scss', 'public/css/pages')

    // NOISE
    .js('resources/js/pages/noise.js', 'public/js/pages')
    .sass('resources/sass/pages/noise.scss', 'public/css/pages')

    // AUTH
    .js('resources/js/pages/auth.js', 'public/js/pages')
    .sass('resources/sass/pages/auth.scss', 'public/css/pages')

    // USERS MANAGEMENT
    .js('resources/js/pages/admin/users.js', 'public/js/pages/admin')
    .sass('resources/sass/pages/admin/users.scss', 'public/css/pages/admin');
