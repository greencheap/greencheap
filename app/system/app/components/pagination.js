export default {

    template: '<ul class="uk-pagination uk-flex-center"></ul>',

    props: {
        value: {
            default: 0,
            type: Number,
        },

        pages: {
            default: 1,
            type: Number,
        },

        replaceState: {
            type: Boolean,
            default: true,
        },

        options: {
            type: Object,
            default: function() {return {}}
        },

        name: {
            default: '',
            type: String
        }
    },

    data() {
        return {
            page: this.value,
        };
    },

    created() {

        var name = this.name || this.$parent.$options.name || this.$parent.$options._componentTag;

        this.key = `${name}.pagination`;

        if (this.page === null && this.$session.get(this.key)) {
            this.$set(this, 'page', this.$session.get(this.key));
        }

        if (this.replaceState) {
            this.$state('page', this.page);
        }
    },

    mounted() {
        const vm = this;

        this.pagination = UIkit.pagination(this.$el, _.extend({
            pages: this.pages,
            currentPage: this.page || 0
        }, this.options));

        UIkit.util.on(this.pagination.$el, 'select.uk.pagination', (e, pagination, page) => {
            vm.$emit('input', Number(page));
        });
    },

    watch: {

        value(page) {
            this.$set(this, 'page', page);
        },

        page(page) {
            this.pagination.selectPage(page || 0);
            this.$session.set(this.key, page || 0);
        },

        pages(pages) {
            if (!this.pages) this.page = 0;
            this.pagination.render(pages);
        },

    },

};
