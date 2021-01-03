const mix = require('laravel-mix');
const webpack = require('webpack');

require('dotenv').config();

require('laravel-mix-purgecss');
require('laravel-mix-polyfill');
require('laravel-mix-modernizr');

mix.webpackConfig({
  plugins: [
    new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
  ]
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the Sass
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
      }
    ]
  }
});

mix.js('resources/js/common.js', 'public/js')
   .js('resources/js/randomForm.js', 'public/js')
   .js('resources/js/dearSanta.js', 'public/js')
   .js('resources/js/organizer.js', 'public/js')
   .js('resources/js/faq.js', 'public/js')
   .modernizr()
   .polyfill({ entryPoints: "all" })
   .extract([
      'vue', 'vue-autosize', 'vue-multiselect',
      'vuelidate', 'vue-i18n', 'vue-recaptcha', 'vuejs-dialog'
    ], 'public/js/vendors-vue.js')
   .extract([
      'jquery', 'jquery-ui', 'jquery.actual', 'jquery.scrollto', 'bootstrap'
    ], 'public/js/vendors-jquery.js')
   .extract([
      'alertifyjs', 'moment', 'papaparse', 'crypto-js'
    ], 'public/js/vendors-ui.js')
   .sass('resources/sass/randomForm.scss', 'public/css')
   .sass('resources/sass/dearSanta.scss', 'public/css')
   .sass('resources/sass/organizer.scss', 'public/css')
   .sass('resources/sass/faq.scss', 'public/css')
   .sass('resources/sass/404.scss', 'public/css')
   .purgeCss({
      content: [
        "app/**/*.php",
        "resources/**/*.html",
        "resources/**/*.js",
        "resources/**/*.jsx",
        "resources/**/*.ts",
        "resources/**/*.tsx",
        "resources/**/*.php",
        "resources/**/*.vue",
        "resources/**/*.twig",
        "node_modules/**/*.vue", // Added line, all the rest is copied from postcss-purgecss-laravel plugin
      ]
   })
   .copy('resources/img', 'public/images')
   .copy('resources/fonts', 'public/fonts');

if (mix.inProduction()) {
    mix.version();
}
