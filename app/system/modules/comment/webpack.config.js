module.exports = [{
    entry: {
        index: "./app/views/index",
        settings: "./app/views/settings",
        service: "./app/views/service",
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
                use: [{
                        loader: "style-loader",
                    },
                    {
                        loader: "css-loader",
                    },
                    {
                        loader: "less-loader",
                    },
                ],
            }
        ],
    },
}, ];