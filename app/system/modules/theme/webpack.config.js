module.exports = [
    {
        entry: {
            theme: "./app/views/theme",
            header: "./app/views/header",
        },
        output: {
            filename: "./app/bundle/[name].js",
        },
        module: {
            rules: [
                { test: /\.html$/, use: "html-loader" },
                { test: /\.vue$/, use: "vue-loader" },
                {
                    test: /\.less$/,
                    use: [
                        {
                            loader: "style-loader",
                        },
                        {
                            loader: "css-loader",
                        },
                        {
                            loader: "less-loader",
                        },
                    ],
                },
            ],
        },
    },
];
