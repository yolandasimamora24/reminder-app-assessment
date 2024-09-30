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

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/admin/backpack-activity/index.js', 'public/js/admin/backpack-activity')
    .js('resources/js/admin/provider/index.js', 'public/js/admin/provider')
    .js('resources/js/admin/provider/edit.js', 'public/js/admin/provider')
    .js('resources/js/admin/consultation/index.js', 'public/js/admin/consultation')
    .js('resources/js/admin/application/index.js', 'public/js/admin/application')
    .js('resources/js/admin/job/index.js', 'public/js/admin/job')
    .js('resources/js/admin/patient/index.js', 'public/js/admin/patient')
    .js('resources/js/admin/appointment/index.js', 'public/js/admin/appointment')
    .js('resources/js/admin/dashboard/index.js', 'public/js/admin/dashboard')
    .css('resources/css/app.css', 'public/css/app.css')
    .css('resources/css/admin/provider/index.css', 'public/css/admin/provider/index.css')
    .css('resources/css/admin/consultation/index.css', 'public/css/admin/consultation/index.css')
    .css('resources/css/admin/consultation/edit.css', 'public/css/admin/consultation/edit.css')
    .css('resources/css/admin/patient/index.css', 'public/css/admin/patient/index.css')
    .css('resources/css/admin/dashboard/index.css', 'public/css/admin/dashboard/index.css')
    .copyDirectory('resources/images', 'public/images');
