/**
 * As our first step, we'll pull in the user's webpack.mix.js
 * file. Based on what the user requests in that file,
 * a generic config object will be constructed for us.
 */
let mix = require('laravel-mix/src/index');

let ComponentFactory = require('laravel-mix/src/components/ComponentFactory');

new ComponentFactory().installAll();

require(Mix.paths.mix());

/**
 * Just in case the user needs to hook into this point
 * in the build process, we'll make an announcement.
 */

Mix.dispatch('init', Mix);

/**
 * Now that we know which build tasks are required by the
 * user, we can dynamically create a configuration object
 * for Webpack. And that's all there is to it. Simple!
 */

let WebpackConfig = require('laravel-mix/src/builder/WebpackConfig');

const config = new WebpackConfig().build();

// Inject sass-loader options
config.module.rules
  .filter(rule => rule.test.test && (rule.test.test('.sass') || rule.test.test('.scss')))
  .forEach(rule => {
    const sassLoader = rule.loaders.find(loader => loader.loader === 'sass-loader');

    if (sassLoader) {
      Object.assign(sassLoader.options, {
        precision: 5,
        implementation: require('node-sass')
      });
    }
  });

// Fix Hot Module Replacement bug
if (Mix.isUsing('hmr')) {
  // Remove leading '/' from entry keys
  config.entry = Object.keys(config.entry)
    .reduce((entries, entry) => {
      entries[entry.replace(/^\//, '')] = config.entry[entry];
      return entries;
    }, {});

  // Remove leading '/' from ExtractTextPlugin instances
  config.plugins
    .forEach((plugin) => {
      if (plugin.constructor.name === 'ExtractTextPlugin') {
        plugin.filename = plugin.filename.replace(/^\//, '');
      }
    });
}

module.exports = config;
