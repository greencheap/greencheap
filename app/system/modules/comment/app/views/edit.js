const Edit = {
    el: '#app',
    name: 'Edit',
    data() {
        return _.merge({
            isEdit: false
        }, window.$data)
    },

    created() {
        this.resource = this.$resource('api/comment{/id}')
    },

    methods: {
        changeEdit() {
            this.isEdit = this.isEdit ? false : true;
        },

        save() {
            this.resource.save({ id: 'save' }, { comment: this.comment, id: this.comment.id }).then((res) => {
                const { query } = res.data
                this.$notify(this.$trans('Saved'))
                if (this.originstatus != 1 && query.status == 1 && this.notify_reply) {
                    const self = this;
                    UIkit.modal.confirm(self.$trans('UIkit confirm!')).then(() => {
                        self.originstatus = query.status
                        self.$http.get('admin/api/comment/sendinformation', {
                            params: {
                                comment: self.comment
                            }
                        }).then((res) => {
                            self.$notify(self.$trans('Mail sent'))
                        }).catch((err) => {
                            self.$notify(err.data, 'danger')
                        })
                    }, () => {});
                }
            }).catch((err) => {
                this.$notify(err.data, 'danger')
            })
        }
    }
}

Vue.ready(Edit)