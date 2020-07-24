const _ = require('lodash');
const glob = require('glob');
const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

// eslint-disable-next-line no-var
var exports = [];

// Define common for all modules
const common = {
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: [/node_modules/, /assets/, /vendor/],
                use: ['babel-loader'],
            },
        ],
    },
    resolve: {
        alias: {
            SystemApp: path.resolve(__dirname, 'app/system/app'),
        },
    },
    externals: {
        vue: 'Vue',
        uikit: 'UIkit',
        'uikit-util': 'UIkit.util',
    },
};

// Define process mode and paths to ignore when processed
// eslint-disable-next-line no-multi-assign
const mode = process.env.NODE_ENV = (process.argv.indexOf('-p') !== -1) ? 'production' : 'development';
const ignore = [
    'packages/**/node_modules/**',
    'packages/**/app/assets/**',
    'packages/**/app/vendor/**',
    'packages/greencheap/**',
    // example - '**/greencheap/blog/**'
];

// Process
glob.sync('{app/modules/**,app/installer/**,app/system/**,packages/**}/webpack.config.js', {
    ignore,
}).forEach((file) => {
    const dir = path.join(__dirname, path.dirname(file));
    // eslint-disable-next-line no-unused-vars
    const pkg = path.join(path.basename(path.dirname(dir)), path.basename(dir));

    // eslint-disable-next-line global-require
    // eslint-disable-next-line import/no-dynamic-require
    exports = exports.concat(require('./' + file).map((config) => {
        // eslint-disable-next-line no-param-reassign
        config = _.merge({
            mode,
            context: dir,
            output: {
                path: dir,
            },
            externals: common.externals,
            resolve: common.resolve,
        }, config);
        // eslint-disable-next-line no-use-before-define
        return build(config);
    }));
});

// Build each webpack.config.js
function build(config) {
    const rules = _.get(config, 'module.rules') || [];
    const loaders = (loader) => rules && rules.filter((e) => e.use === loader || e.use.indexOf(loader) !== -1).length;

    // Merge rules, add common rules to first
    _.set(config, 'module.rules', _.concat([], common.module.rules, rules));

    // Push VueLoader plugin
    if (loaders('vue-loader')) {
        // eslint-disable-next-line no-param-reassign
        config.plugins = (typeof config.plugins !== 'undefined') ? config.plugins : [];
        config.plugins.push(new VueLoaderPlugin());
    }

    return config;
}

module.exports = exports;
