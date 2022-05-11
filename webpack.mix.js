const mix = require('laravel-mix')
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

mix.babelConfig({
    'presets': [
      [
        '@babel/preset-env',
        {
          'useBuiltIns': 'entry',
          'targets': {
            'browsers': ['ie >= 10', '> 1%'],
          },
        },
      ],
    ],
  },
)


mix.js('resources/js/app.js', 'public/js/app.js')
  .sass('resources/sass/app.scss', 'public/css')
  .sass('resources/sass/app-front.scss', 'public/css')
  .vue({version: 2})
