const categories = {
    el: '#app',
    name: 'Categories',
    data() {
        return _.merge({
            categories: false,
            config: {
                filters: this.$session.get('filters.config', {
                    order: 'date desc',
                    limit: 25,
                    type: '',
                    sub_category: 0,
                }),
            },
            pages: 0,
            count: '',
            selected: [],
            canEditAll: false,
        }, window.$data);
    },

    mounted() {
        this.resource = this.$resource('admin/api/categories{/id}');
        this.$watch('config.page', this.load, {
            immediate: true,
        });
    },

    watch: {
        'config.filters': {
            handler(filters) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }
                this.$session.set('filters.config', filters);
            },
            deep: true,
        },
    },

    methods: {
        load(subCategory = 0) {
            this.$http.get('admin/api/categories/get', {
                params: {
                    filters: this.config.filters,
                    page: this.config.page,
                    sub_category: subCategory,
                },
            }).then((res) => {
                // eslint-disable-next-line no-shadow
                const { categories, pages, count } = res.data;
                this.$set(this, 'categories', categories);
                this.$set(this, 'pages', pages);
                this.$set(this, 'count', count);
            });
        },

        save(category) {
            this.$http.post('admin/api/categories/save', {
                id: category.id,
                data: category,
            }).then(() => {
                this.load();
                this.$notify(this.$trans('Categories saved.'));
            }).catch((err) => {
                this.$notify(err.bodyText, 'danger');
            });
        },

        remove() {
            this.$http.delete('admin/api/categories/bulk', {
                params: {
                    ids: this.selected,
                },
            }).then(() => {
                this.load();
                this.$notify(this.$trans('Categories deleted.'));
            });
        },

        setConfigType(type) {
            this.config.filters.type = type;
        },

        setConfigSub(id) {
            this.selected = [];
            this.config.filters.sub_category = id;
        },

        clearSubCategory() {
            this.setConfigSub(0);
        },

        status(status) {
            const categories = this.getSelected();

            categories.forEach((category) => {
                // eslint-disable-next-line no-param-reassign
                category.status = status;
                this.save(category);
            });
        },

        getSelected() {
            return this.categories.filter((category) => this.selected.indexOf(category.id) !== -1, this);
        },
    },
};

Vue.ready(categories);
