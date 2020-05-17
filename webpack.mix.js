const mix = require('laravel-mix');
const glob = require('glob');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsDir(query, cb) {
    (glob.sync('resources/assets/' + query) || []).forEach(f => {
        f = f.replace(/[\\\/]+/g, '/');
        // f = f.replace('resources/assets/vendor', 'public/webdist/vendor');
        cb(f, f.replace('resources/assets', 'public/webdist'));
    });
}

const sassOptions = {
    precision: 5,
    implementation: () => require('node-sass')
};

// Core stylesheets
mix.sass('resources/assets/vendor/sass/bootstrap.scss', 'public/webdist/vendor/css', sassOptions)
   .sass('resources/assets/vendor/sass/appwork.scss', 'public/webdist/vendor/css', sassOptions)
   .sass('resources/assets/vendor/sass/theme-corporate.scss', 'public/webdist/vendor/css', sassOptions)
   .sass('resources/assets/vendor/sass/colors.scss', 'public/webdist/vendor/css', sassOptions)
   .sass('resources/assets/vendor/sass/uikit.scss', 'public/webdist/vendor/css', sassOptions);

// Core javascripts
mixAssetsDir('vendor/js/**/*.js', (src, dest) => mix.scripts(src, dest));

// Libs
mixAssetsDir('vendor/libs/**/*.js', (src, dest) => mix.scripts(src, dest));
mixAssetsDir('vendor/libs/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/\.scss$/, '.css'), sassOptions));

// Pages
mixAssetsDir('vendor/sass/pages/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\\|\/)sass(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// Fonts
mixAssetsDir('vendor/fonts/*.css', (src, dest) => mix.copy(src, dest));
mixAssetsDir('vendor/fonts/*/*', (src, dest) => mix.copy(src, dest));
mixAssetsDir('css/**/*.css', (src, dest) => mix.copy(src, dest));

/*
 |--------------------------------------------------------------------------
 | Application assets
 |--------------------------------------------------------------------------
 */

mix.js('resources/assets/js/application.js', 'public/webdist/js')
   .sass('resources/assets/sass/application.scss', 'public/webdist/css')
   .copyDirectory("app/MainApp/resources/assets", "public/assets");

mix.version();