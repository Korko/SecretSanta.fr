const mix = require('laravel-mix');
const webpack = require('webpack');

const MiniCssExtractPlugin = require("mini-css-extract-plugin");

require('dotenv').config();

//require('laravel-mix-purgecss');
//require('laravel-mix-polyfill');

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

mix.override(webpackConfig => {
  // BUG: laravel-mix doesn't handle file-loader's default esModule:true setting properly causing
  // corrupted .ttf trying to be loaded by the browser.
  // WORKAROUND: Override mixs and turn off esModule support on fonts.
  // FIX: When laravel-mix fixes their bug AND laravel-mix updates to the fixed version
  // this can be removed
  webpackConfig.module.rules.forEach(rule => {
    if (rule.test.toString() === '/(\.(woff2?|ttf|eot|otf)$|font.*\.svg$)/') {
      if (Array.isArray(rule.use)) {
        rule.use.forEach(ruleUse => {
          if (ruleUse.loader === 'file-loader') {
            ruleUse.options.esModule = false;
          }
        });
      }
    }
  });
});

mix.webpackConfig({
  optimization: {
    providedExports: false,
    sideEffects: false,
    usedExports: false
  },
  plugins: [
    new webpack.IgnorePlugin({
        resourceRegExp: /^\.\/locale$/,
        contextRegExp: /moment$/,
    }),
    new MiniCssExtractPlugin(),
    new webpack.DefinePlugin({
        __VUE_I18N_FULL_INSTALL__: true,
        __VUE_I18N_LEGACY_API__: false,
        __INTLIFY_PROD_DEVTOOLS__: false
    })
  ],
  module: {
    rules: [
      {
        // Matches all PHP or JSON files in `resources/lang` directory.
        test: /resources[\\\/]lang.+\.(php|json)$/,
        loader: 'laravel-localization-loader',
      },
//      {
//        test: /\.(sa|sc|c)ss$/,
//        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
//      },
      {
        test: /\.(jpg|png|webp)$/,
        loader: 'file-loader',
        options: {
          outputPath: 'images',
          name: '[name].[ext]?[contenthash]',
          esModule: false
        },
      }
    ]
  }
});

mix.options({
    processCssUrls: true,
    terser: {
        extractComments: false
    },
    purifyCss: false,
    postCss: [require('autoprefixer')],
    clearConsole: false,
    cssNano: {
        discardComments: {removeAll: true},
    },
    imgLoaderOptions: false
});

mix.js('resources/js/common.js', 'public/js')
   .js('resources/js/app.js', 'public/js')
//   .js('resources/js/dearSanta.js', 'public/js')
//   .js('resources/js/organizer.js', 'public/js')
   .vue({
      extractStyles: false,
      globalStyles: false
    })
//   .polyfill({ entryPoints: "all" })
   .extract()
   .sass('resources/sass/app.scss', 'public/css')
   .sass('resources/sass/404.scss', 'public/css')
   .sass('resources/sass/vendor.scss', 'public/css')
//   .purgeCss({
//      content: [
//        "app/**/*.php",
//        "resources/**/*.blade.php",
//        "resources/**/*.html",
//        "resources/**/*.js",
//        "resources/**/*.jsx",
//        "resources/**/*.ts",
//        "resources/**/*.tsx",
//        "resources/**/*.php",
//        "resources/**/*.vue",
//        "resources/**/*.twig",
//        "storage/framework/views/*.php",
//        "node_modules/**/*.vue", // Added line, all the rest is copied from postcss-purgecss-laravel plugin
//      ]
//   })
   .copy('resources/images/logo_black.png', 'public/images'); // Meta image for social networks

if (mix.inProduction()) {
    mix.version();
}
