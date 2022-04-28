const mix = require("laravel-mix");

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
    presets: [
        [
            "@babel/preset-env",
            {
                useBuiltIns: "entry",
                targets: {
                    browsers: ["ie >= 10", "> 1%"]
                }
            }
        ]
    ]
});

mix.webpackConfig({
    devServer: {
        host: 'localhost',
        port: 3000,
    },
});

mix.override(webpackConfig => {
    webpackConfig.module.rules[2].use[0].options.esModule = false;
});

mix.js("resources/js/app.js", "js")
    .vue()
    .sass("resources/sass/app.scss", "public/css")
    .options({ processCssUrls: false, uglify: { compress: false } })
    .sass("resources/sass/app-front.scss", "public/css")
    .options({ processCssUrls: false, uglify: { compress: false } })
    .sass("resources/sass/portal.scss", "public/css")
    .options({ processCssUrls: false, uglify: { compress: false } })
    .sass("resources/sass/video-lesson.scss", "public/css")
    .options({ processCssUrls: false, uglify: { compress: false } });