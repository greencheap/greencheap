module.exports = [{
    entry: {
        debugbar: './app/debugbar',
    },
    output: {
        filename: './app/bundle/[name].js',
        library: 'Debugbar',
    },
    module: {
        rules: [{ test: /\.vue$/, use: 'vue-loader' }],
    },
}];
