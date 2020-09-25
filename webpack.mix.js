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

mix.webpackConfig((webpack) => {
    return {
        resolve: {
            extensions: [".ts", ".js", ".vue"],
            alias: {
                "@jsFolder": path.resolve(__dirname , 'js')
            }
        }
    };
});

mix.ts('resources/js/app.ts', 'public/js')
    .sass('resources/css/app.scss', 'public/css');
