module.exports = [

    {
        entry: {
            "editor": "./app/components/editor.vue"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Editor"
        },
        module: {
            rules: [
                { test: /\.vue$/, use: "vue-loader" }
            ]
        }
    }

];
