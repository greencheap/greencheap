module.exports = [
    {
        entry: {
            categories: './app/views/categories',
            'categories-edit': './app/views/categories-edit',
        },
        output: {
            filename: './app/bundle/[name].js',
        },
        module: {
            rules: [
                { test: /\.html$/, use: 'html-loader' },
                { test: /\.vue$/, use: 'vue-loader' },
            ],
        },
    }
];
