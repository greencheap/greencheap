<template>
    <div>
        <v-modal ref="login" modal-small center>
            <div class="uk-text-center">
                <form ref="loginEle" class="tm-form" @submit.prevent="login">
                    <div class="uk-card uk-card-default uk-panel" style="padding: 20px;">
                        <div>
                            <p class="uk-h3 uk-margin-remove-top">
                                {{ 'Autorization' | trans }}
                            </p>
                        </div>

                        <div class="uk-margin">
                            <span class="uk-margin-small-top uk-text-meta uk-text-danger" style="letter-spacing: -1px;">{{ 'Session expired. Please log in again.' | trans }}</span>
                        </div>

                        <div class="uk-margin">
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon" uk-icon="icon: user" />
                                <input
                                    v-model="credentials.username"
                                    class="uk-input"
                                    type="text"
                                    name="credentials[username]"
                                    :placeholder="'Username' | trans"
                                    autofocus
                                >
                            </div>
                        </div>

                        <div class="uk-margin">
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon" uk-icon="icon: lock" />
                                <input
                                    ref="password"
                                    v-model="credentials.password"
                                    class="uk-input"
                                    type="password"
                                    name="credentials[password]"
                                    :placeholder="'Password' | trans"
                                >
                            </div>
                        </div>

                        <button class="uk-button uk-button-primary uk-width-1-1 uk-margin-medium uk-margin-remove-bottom">
                            <span>{{ 'Sign in' | trans }}</span>
                        </button>

                        <p class="uk-margin-remove-bottom">
                            <label class=""><input v-model="remember" class="uk-checkbox" type="checkbox"><span class="uk-margin-small-left">{{ 'Remember Me' | trans }}</span></label>
                        </p>
                    </div>
                </form>
            </div>
        </v-modal>
    </div>
</template>

<script>
export default {

    data() {
        return {
            credentials: {},
            remember: false,
        };
    },

    created() {
        this.$mount();

        this.promise = this.$promise(function (fulfill, reject) {
            this.fulfill = fulfill;
            this.reject = reject;
        });
    },

    mounted() {
        const vm = this;

        this.$refs.login.open();

        UIkit.util.on(this.$refs.login.modal.$el, 'hide', () => {
            vm.reject();
            vm.$destroy();
        });
    },

    methods: {

        login() {
            const login = function () {
                return this.$http.post('user/authenticate', {
                    credentials: this.credentials,
                    _remember_me: this.remember,
                }, { headers: { 'X-LOGIN': 'true' } });
            }.bind(this);

            login().then(null, function (res) {
                if (res.data.csrf) {
                    this.$cache.set('_csrf', res.data.csrf);
                    this.$cache.set('_session', window.$app.csrf);
                    this.$session.flush();

                    return login();
                }

                return Vue.Promise.reject(res);
            }).then(function (res) {
                this.$cache.set('_csrf', res.data.csrf);
                this.fulfill();
                this.$refs.login.close();
            }, function (res) {
                this.$notify(res.data, 'danger');
                this.$refs.loginEle.classList.remove('uk-animation-shake');
                this.$nextTick(function () {
                    this.$refs.loginEle.classList.add('uk-animation-shake');
                });
                this.$refs.password.focus();
            });
        },

    },

};

</script>
