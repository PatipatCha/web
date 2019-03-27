const { mix } = require('laravel-mix');

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


mix.webpackConfig({
    resolve: {
        alias: {
            jquery: "jquery/src/jquery"
        }
    },

});


//Javascripts
mix.js('resources/assets/js/app.js', 'public/js')
    .extract(['jquery', 'vue', 'vuex', 'bootstrap-sass', 'accounting', 'store', 'animate.css']);

//Styles
mix.sass('resources/assets/sass/app.scss', '../resources/assets/app.css');
mix.combine([
    'public/assets/css/bootstrap.min.css',
    'public/assets/css/makro.css',
    'public/assets/css/step-peyment.css',
    'public/assets/css/bootstrap.offcanvas.css',
    'public/assets/css/owl.carousel.min.css',
    'public/assets/css/ie10-viewport-bug-workaround.css',
    'resources/assets/app.css'
],  'public/css/app.css');

mix.options({
    processCssUrls: false
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

//If build production, add version to file
//if (mix.config.inProduction) { //Comment for dev
if (mix.inProduction()) {
    mix.version();
    mix.options({
        uglify: {
            uglifyOptions: {
                compress: {
                    drop_console: true,
                }
            }
        },
    });
}