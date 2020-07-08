import { ValidationObserver, VInput } from 'SystemApp/components/validation.vue';
import UserSettings from '../../components/user-settings.vue';

window.User = {

    name: 'user-edit',

    el: '#user-edit',

    mixins: [Theme.Mixins.Helper],

    data() {
        return _.extend({ sections: [], form: {}, processing: false }, window.$data);
    },

    provide: {
        '$components': {
            'v-input': VInput
        }
    },

    watch:{
        'user.data.avatar':{
            handler(){
                this.save();
                return;
            },
            deep:false
        }
    },

    created() {
        const sections = [];

        _.forIn(this.$options.components, (component, name) => {
            if (component.section) {
                sections.push(_.extend({ name, priority: 0 }, component.section));
            }
        });

        this.$set(this, 'sections', _.sortBy(sections, 'priority'));
    },

    mounted() {
        this.tab = UIkit.tab(this.$refs.tab, { connect: '#user-content' });
    },

    methods: {

        async submit() {
            const isValid = await this.$refs.observer.validate();
            if (isValid) {
                this.processing = true;
                this.save();
            }
        },

        save() {
            const data = { user: this.user };
            const vm = this;

            this.$trigger('save:user', data);

            this.$resource('api/user{/id}').save({ id: this.user.id }, data).then(function (res) {
                if (!this.user.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/user/edit', { id: res.data.user.id }));
                }

                this.$set(this, 'user', res.data.user);

                this.$notify('User saved.');

            }, function (res) {
                this.$notify(res.data, 'danger');
            }).then(function() {
                setTimeout(() => {
                    vm.processing = false;
                }, 500);
            });
        },

    },

    components: {

        ValidationObserver,
        settings: UserSettings,

    },

};

Vue.ready(window.User);
