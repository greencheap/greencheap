module.exports = [

    {
        entry: {
            dashpanel: './app/views/dashpanel'
        },
        output: {
            filename: './app/bundle/[name].js',
        },
        module: {
            rules: [
                { test: /\.vue$/, use: 'vue-loader' },
                {
                    test: /\.css$/,
                    use: [
                        'vue-style-loader',
                        'css-loader'
                    ]
                }    
            ],
        },
    },

];
