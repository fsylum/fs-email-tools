let mix = require('laravel-mix');

mix.js('assets/src/js/admin.js', 'js')
    .sass('assets/src/scss/admin.scss', 'css')
    .setPublicPath('assets/dist');
