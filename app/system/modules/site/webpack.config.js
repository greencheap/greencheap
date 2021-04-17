module.exports = [

    {
        entry: {
            'panel-link': './app/components/panel-link.vue',
        },
        output: {
            filename: './app/bundle/[name].js',
            library: 'Links',
        },
        module: {
            rules: [
                { test: /\.vue$/, use: 'vue-loader' },
            ],
        },
    },

    {
        entry: {
            edit: './app/views/edit',
            index: './app/views/index',
            'input-link': './app/components/input-link.vue',
            'input-tree': './app/components/input-tree.vue',
            'link-page': './app/components/link-page.vue',
            'node-page': './app/components/node-page.vue',
            'node-meta': './app/components/node-meta.vue',
            settings: './app/views/settings',
            'widget-menu': './app/components/widget-menu.vue',
            'widget-text': './app/components/widget-text.vue',
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
    },

];
