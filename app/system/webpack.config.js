const assets = `${__dirname}/../assets`;

module.exports = [
    {
        entry: {
            vue: "./app/vue",
        },
        output: {
            filename: "./app/bundle/[name].js",
        },
        resolve: {
            alias: {},
        },
        module: {
            rules: [
                { test: /\.vue$/, use: "vue-loader" },
                { test: /\.json$/, use: "json-loader" },
                { test: /\.html$/, use: "html-loader" },
                { test: /\.css$/, use: ["vue-style-loader", "css-loader"] },
            ],
        },
    },
];
