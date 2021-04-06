const Edit = {
    el: "#app",
    name: "Edit",
    data() {
        return _.merge(
            {
                isEdit: false,
            },
            window.$data
        );
    },

    created() {
        this.resource = this.$resource("api/comment{/id}");
    },

    methods: {
        changeEdit() {
            this.isEdit = this.isEdit ? false : true;
        },

        save(event) {
            this.$loader(event);
            this.resource
                .save({ id: "save" }, { comment: this.comment, id: this.comment.id })
                .then((res) => {
                    const { query } = res.data;
                    this.$notify(this.$trans("Saved"));
                    if (this.originstatus != 1 && query.status == 1 && this.notify_reply) {
                        const self = this;
                        UIkit.modal.confirm(self.$trans("Do you want to inform the user that their comment has been approved?")).then(
                            () => {
                                self.originstatus = query.status;
                                self.$http
                                    .get("admin/api/comment/sendinformation", {
                                        params: {
                                            comment: self.comment,
                                        },
                                    })
                                    .then((res) => {
                                        self.$notify(self.$trans("Mail sent"));
                                    })
                                    .catch((err) => {
                                        self.$notify(err.data, "danger");
                                    });
                            },
                            () => {}
                        );
                    }
                    this.$loader(event, false);
                })
                .catch((err) => {
                    this.$notify(err.data, "danger");
                    this.$loader(event, false);
                });
        },
    },
};

Vue.ready(Edit);
