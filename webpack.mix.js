const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .vue() // Vue 2 ise
    .sass('resources/sass/app.scss', 'public/css');
