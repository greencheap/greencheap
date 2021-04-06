const Comment = {
    el: "#app",
    name: "Comment",
    data() {
        return _.merge(
            {
                comments: false,
                config: {
                    filter: this.$session.get("comments.filter", { order: "created desc", limit: 10, developer: true }),
                },
                pages: 0,
                count: "",
                selected: [],
                canEditAll: false,
            },
            window.$data
        );
    },

    mounted() {
        this.resource = this.$resource("api/comment{/id}");
        this.$watch("config.page", this.load, { immediate: true });
    },

    watch: {
        "config.filter": {
            handler(filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }
                this.$session.set("comments.filter", filter);
            },
            deep: true,
        },
    },

    computed: {
        statusOptions() {
            const options = _.map(this.statuses, (status, id) => ({ text: status, value: id }));

            return [{ label: this.$trans("Filter by"), options }];
        },

        users() {
            const options = _.map(this.authors, (author) => ({ text: author.username, value: author.id }));

            return [{ label: this.$trans("Filter by"), options }];
        },

        getTypes() {
            const options = _.map(this.types, (type) => ({ text: type.type, value: type.type }));
            return [{ label: this.$trans("Filter by"), options }];
        },
    },

    methods: {
        active(comment) {
            return this.selected.indexOf(comment.id) != -1;
        },

        load() {
            this.resource.query({ filter: this.config.filter, page: this.config.page }).then(function (res) {
                const { data } = res;
                this.$set(this, "comments", data.comments);
                this.$set(this, "pages", data.pages);
                this.$set(this, "count", data.count);
                this.$set(this, "selected", []);
            });
        },

        getSelected() {
            return this.comments.filter(function (comment) {
                return this.selected.indexOf(comment.id) !== -1;
            }, this);
        },

        getStatusText(comment) {
            return this.statuses[comment.status];
        },

        save(comment) {
            this.resource.save({ id: "save" }, { comment: comment, id: comment.id }).then(() => {
                this.load();
                this.$notify("Comment saved.");
            });
        },

        toggleStatus(comment) {
            comment.status = comment.status === 1 ? 0 : 1;
            this.save(comment);
        },

        status(status) {
            const comments = this.getSelected();

            comments.forEach((comment) => {
                comment.status = status;
            });

            this.resource.save({ id: "bulk" }, { comments }).then(function () {
                this.load();
                this.$notify("Comments saved.");
            });
        },

        remove() {
            const comments = this.getSelected();

            this.resource.delete({ id: "bulk" }, { comments }).then(function () {
                this.load();
                this.$notify("Comments deleted.");
            });
        },
    },
};
export default Comment;
Vue.ready(Comment);
