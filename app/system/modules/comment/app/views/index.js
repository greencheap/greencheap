const Comment = {
    el: '#app',
    name: 'Comment',
    data() {
        return _.merge({

        }, window.$data)
    }
}
export default Comment;
Vue.ready(Comment)