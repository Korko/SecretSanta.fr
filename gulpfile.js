var elixir = require('laravel-elixir');

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
    mix.browserify([
        'main.js', 'app.js'
    ], 'public/assets/bundle.js');
    mix.sass([
        'main.scss'
    ], 'public/assets/bundle.css', {
        includePaths: [
            './node_modules/bootstrap-sass/assets/stylesheets/',
            './node_modules/font-awesome/scss/',
            './node_modules/alertify.js/src/sass/',
            './resources/assets/vendor/bootstrap-languages/'
        ]
    });
    mix.copy('resources/assets/img', 'public/img');
    mix.copy('resources/assets/vendor/bootstrap-languages/languages.png', 'public/assets/img');
    mix.copy('node_modules/bootstrap-sass/assets/fonts', 'public/assets/fonts');
    mix.copy('node_modules/font-awesome/fonts', 'public/assets/fonts');
});
