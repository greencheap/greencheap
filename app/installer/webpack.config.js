module.exports = [

    {
        entry: {
            installer: './app/views/installer.vue',
            extensions: './app/views/extensions',
            themes: './app/views/themes',
            update: './app/views/update',
        },
        output: {
            filename: './app/bundle/[name].js',
        },
        module: {
            rules: [
                { test: /\.vue$/, use: 'vue-loader' },
            ],
        },
    },

];
