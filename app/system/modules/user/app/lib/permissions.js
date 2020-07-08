export default {

    data() {
        return window.$data;
    },

    created() {
        this.Roles = this.$resource('api/user/role{/id}');

        this.debounced = [];

        this.saveCb = _.debounce(() => {
            this.Roles.save({ id: 'bulk' }, { roles: this.debounced }).then(function () {
                this.$notify('Permissions saved');
            });

            this.debounced = [];
        }, 1000);
    },

    computed: {

        authenticated() {
            return this.roles.filter(role => role.authenticated)[0];
        },

    },

    methods: {

        savePermissions(role) {
            if (!_.find(this.debounced, 'id', role.id)) {
                this.debounced.push(role);
            }

            this.saveCb();
        },

        addPermission(role, permission) {
            return !role.administrator ? role.permissions.push(permission) : null;
        },

        hasPermission(role, permission) {
            return role.permissions.indexOf(permission) !== -1;
        },

        isInherited(role, permission) {
            return !role.locked && this.hasPermission(this.authenticated, permission);
        },

        showFakeCheckbox(role, permission) {
            return role.administrator || (this.isInherited(role, permission) && !this.hasPermission(role, permission));
        },

    },

};
