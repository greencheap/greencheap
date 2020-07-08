import { ValidationObserver, VInput } from 'SystemApp/components/validation.vue';

var UserProfile = {

    el: '#user-profile',

    data() {
        return _.merge({
            user: { password: {} },
            hidePassword: true,
            changePassword: false,
            view: {
                type: 'icon',
                containerClass: 'uk-margin',
                class: 'uk-input uk-form-width-large',
                icon: () => this.hidePassword ? 'lock' : 'unlock',
                iconClick: () => { this.hidePassword = !this.hidePassword },
                iconTag: 'a',
                iconDir: 'right',
            }
        }, window.$data);
    },

    watch: {
        changePassword(val) {
            if (val) {
                this.$nextTick(()=>{
                    var icons  = this.$el.querySelectorAll('a.uk-form-icon'),
                        height = this.$el.getElementsByClassName('uk-input')[0].offsetHeight;
                    if (icons.length && height) {
                        icons.forEach((icon) => { icon.style.height = height + 'px' })
                    }
                })
            }
        }
    },

    methods: {

        async submit() {
            const isValid = await this.$refs.observer.validate();
            if (isValid) {
                this.save();
            }
        },

        save() {
            this.$http.post('user/profile/save', { user: this.user }).then(function () {
                this.$notify(this.$trans('Profile Updated'), 'success');
            }, function (res) {
                this.$notify(res.data, 'danger');
            });
        },

    },

    components: {
        ValidationObserver,
        VInput
    }

};

export default UserProfile;

Vue.ready(UserProfile);
