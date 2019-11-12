var gulp = require('gulp'),
    shell = require('gulp-shell'),
    elixir = require('laravel-elixir');

require('laravel-elixir-vueify');

var Task = elixir.Task;
elixir.extend('langjs', function(path) {
    new Task('langjs', function() {
        gulp.src('').pipe(shell('php artisan lang:js --no-lib ' + (path || 'public/js/messages.js')));
    });
});

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.langjs('resources/assets/js/messages.js');

    mix.browserify([
        'main.js', 'randomForm.js', 'script.js'
    ], 'public/assets/randomForm.js');

    mix.browserify([
        'main.js', 'dearSanta.js', 'script.js'
    ], 'public/assets/dearSanta.js');

    var sassOptions = {
        includePaths: [
            './node_modules/bootstrap-sass/assets/stylesheets/',
            './node_modules/font-awesome/scss/',
            './node_modules/alertify.js/src/sass/',
            './node_modules/vue-multiselect/dist/',
            './node_modules/jquery-ui-browserify/themes/base/',
            './resources/assets/vendor/bootstrap-languages/'
        ]
    };

    mix.sass([
        'layout.scss'
    ], 'public/assets/layout.css', sassOptions);

    mix.sass([
        'randomForm.scss'
    ], 'public/assets/randomForm.css', sassOptions);

    mix.sass([
        'dearSanta.scss'
    ], 'public/assets/dearSanta.css', sassOptions);

    mix.copy('resources/assets/img', 'public/assets/images');
    mix.copy('node_modules/bootstrap-sass/assets/fonts', 'public/assets/fonts');
    mix.copy('node_modules/font-awesome/fonts', 'public/assets/fonts');
    mix.copy('node_modules/jquery-ui-browserify/themes/base/images', 'public/assets/images');
});
