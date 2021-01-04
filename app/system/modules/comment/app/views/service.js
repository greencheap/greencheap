const Service = {
    el: '#comment_service',
    name: 'CommentService',
    data() {
        return _.merge({
            comments:  [],
            comment: false,
            msg: '',
            users: [],
            config: {
                filter: { parent_id: 0, order: 'created desc' },
                page: 0
            },
            pages: 0,
            count: '',
        }, window.$comment_service)
    },

    created() {
        this.resource = this.$resource('api/comment{/id}')
        this.getUsers();

        this.draft = _.merge(this.draft, {
            user_id: this.user_id,
            own_id: this.service.own_id,
            type: this.service.type,
            status: this.getCommentStatus,
            data: {
                type_url: this.service.type_url
            },
        })
        const draftTemplate = this.draft;
        this.$set(this, 'comment', draftTemplate)
    },

    mounted() {
        this.load();
    },

    methods: {
        load() {
            this.resource.query({ filter: _.merge(this.service, this.config.filter), page: this.config.page }).then((res) => {
                const data = res.data;
                this.$set(this, 'pages', data.pages)
                this.$set(this, 'count', data.count)
                this.$set(this, 'comments', data.comments)
            }).catch((err) => {
                this.$notify(err.data, 'danger')
            })
        },

        sendComment() {
            const textAreaElement = document.getElementById('commentTextAreaAlert');
            if (!this.comment.content.length) {
                textAreaElement.style.display = 'block'
                return;
            } else {
                textAreaElement.style.display = 'none'
            }

            this.resource.save({ id: 'save' }, { comment: this.comment, id: this.comment.id })
                .then(() => {
                    this.msg = this.$trans('Saved');
                    this.comment = _.merge(this.draft, {
                        user_id: this.user_id,
                        own_id: this.service.own_id,
                        type: this.service.type,
                        status: this.getCommentStatus,
                        parent_id: 0,
                        data: {
                            type_url: this.service.type_url
                        },
                        content: ''
                    })
                    this.load();
                }).catch((err) => {
                    this.$notify(err.data, 'danger')
                })
        },

        getUsers() {
            this.resource.query({ id: 'getusers' }).then((res) => {
                const { users } = res.data
                this.$set(this, 'users', users)
            })
        },

        setReply(parent_id) {
            this.comment.parent_id = parent_id;
        },

        cancelReply() {
            this.comment.parent_id = 0;
        }
    },

    components: {
        Mentionable
    }
}


import { Mentionable } from 'vue-mention'
export default Service
Vue.ready(Service)