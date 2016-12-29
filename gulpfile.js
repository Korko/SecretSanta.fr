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
        'main.js', 'randomForm.js'
    ], 'public/assets/randomForm.js');

    mix.browserify([
        'dearSanta.js'
    ], 'public/assets/dearSanta.js');

    var sassOptions = {
        includePaths: [
            './node_modules/bootstrap-sass/assets/stylesheets/',
            './node_modules/font-awesome/scss/',
            './node_modules/alertify.js/src/sass/',
            './node_modules/multiple-select/',
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

    mix.copy('resources/assets/img', 'public/img');
    mix.copy('resources/assets/vendor/bootstrap-languages/languages.png', 'public/assets/img');
    mix.copy('node_modules/bootstrap-sass/assets/fonts', 'public/assets/fonts');
    mix.copy('node_modules/font-awesome/fonts', 'public/assets/fonts');
    mix.copy('node_modules/multiple-select/multiple-select.png', 'public/assets');
});
