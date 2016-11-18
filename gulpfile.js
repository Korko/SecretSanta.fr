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
    ]);
    mix.sass([
        '../vendor/bootstrap-languages/languages.css',
        'main.scss'
    ]);
    mix.copy('resources/assets/img', 'public/img');
    mix.copy('resources/assets/vendor/bootstrap-languages/languages.png', 'public/img');
    mix.copy('node_modules/bootstrap-sass/assets/fonts', 'public/fonts');
    mix.copy('node_modules/font-awesome/fonts', 'public/fonts');
});
