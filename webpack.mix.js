let mix = require('laravel-mix');

mix.js('assets/src/js/app.js', 'js')
    .setPublicPath('assets/dist');
