module.exports = [

    {
        entry: {
            'panel-finder': './app/components/panel-finder.vue',
            'link-storage': './app/components/link-storage.vue',
        },
        output: {
            filename: './app/bundle/[name].js',
            library: 'Finder',
        },
        module: {
            rules: [
                { test: /\.html$/, use: 'html-loader' },
                { test: /\.vue$/, use: 'vue-loader' },
            ],
        },
    },

];
