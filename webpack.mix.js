const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.autoload({
  jquery: ['$', 'jQuery', 'window.jQuery']
});

mix.webpackConfig({
  module: {
    rules: [
      {
        // Matches all PHP or JSON files in `resources/lang` directory.
        test: /resources[\\\/]lang.+\.(php|json)$/,
        loader: 'laravel-localization-loader',
      },
      {
        test: /resources\/js\/partials\/modernizr\.js$/,
        loader: 'webpack-modernizr-loader'
      }
    ]
  }
});

mix.js('resources/js/common.js', 'public/js')
   .js('resources/js/randomForm.js', 'public/js')
   .js('resources/js/dearSanta.js', 'public/js')
   .js('resources/js/organizer.js', 'public/js')
   .extract()
   .sass('resources/sass/randomForm.scss', 'public/css')
   .sass('resources/sass/dearSanta.scss', 'public/css')
   .sass('resources/sass/organizer.scss', 'public/css')
   .copy('resources/img', 'public/images');

if (mix.inProduction()) {
    mix.version();
}
