const Settings = {
    el: '#app',
    name: 'Settings',
    data() {
        return _.merge({

        }, window.$data)
    },

    methods: {
        save() {
            this.$http.post('admin/system/settings/config', { name: 'system/comment', config: this.config }).then(() => {
                this.$notify('Settings saved.');
            }).catch((err) => {
                this.$notify(err.data, 'danger');
            });
        }
    }
}

export default Settings
Vue.ready(Settings)