module.exports = [
    {
        entry: {
            theme: './js/main',
        },
        output: {
            filename: './js/[name].js',
        },
        module: {
            rules: [
                { test: /\.html$/, use: 'html-loader' },
                { test: /\.vue$/, use: 'vue-loader' },
            ],
        },
    },
];
