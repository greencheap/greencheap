<template>
    <div class="uk-flex uk-flex-center uk-flex-middle tm-background uk-height-viewport">
        <installer-steps :steps="steps" :current="step">
            <template v-slot:start="{ step }">
                <div :step="step" class="uk-text-center">
                    <div class="uk-panel uk-padding-small">
                        <a @click="gotoStep('language')">
                            <img :src="$url('/app/system/assets/images/logo/fav-white.svg')" width="100" alt="GreenCheap">
                            <div class="uk-margin">
                                <svg class="tm-arrow" width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                                    <line fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" x1="2" y1="18" x2="36" y2="18"/>
                                    <polyline fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="26.071,6.5 37.601,18.03 26,29.631 "/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>
            </template>
            <template v-slot:language="{ step }">
                <div :step="step" class="uk-card uk-card-default uk-card-body">

                    <h1 class="uk-card-title uk-text-center">{{ 'Choose language' | trans }}</h1>
                    <div class="uk-margin uk-text-muted uk-text-center">{{ "Select your site language." | trans }}</div>

                    <form @submit.prevent="stepLanguage">
                        <select id="selectbox" class="uk-width-1-1 uk-select" size="10" v-model="locale">
                            <option v-for="(lang, key) in locales" :key="key" :value="key">{{ lang }}</option>
                        </select>

                        <div class="uk-margin uk-text-right uk-margin-remove-bottom">
                            <button class="uk-button uk-button-primary" type="submit" autofocus="">
                                <span class="uk-text-middle">{{ 'Next' | trans }}</span>
                                <span class="uk-margin-small-left" v-html="helper.iconRight"></span>
                            </button>
                        </div>
                    </form>

                </div>
            </template>
            <template v-slot:database="{ step, passes }">
                <div :step="step" class="uk-card uk-card-default uk-card-body">

                    <h1 class="uk-card-title uk-text-center">{{ 'Connect database' | trans }}</h1>
                    <div class="uk-margin uk-text-muted uk-text-center">{{ 'Enter your database connection details.' | trans }}</div>

                    <div class="uk-alert uk-alert-danger uk-margin uk-text-center" v-show="message"><p>{{ message }}</p></div>

                    <form class="uk-form-horizontal tm-form-horizontal" @submit.prevent="passes(stepDatabase)">
                        <div class="uk-margin">
                            <label for="form-dbdriver" class="uk-form-label">{{ 'Driver' | trans }}</label>
                            <div class="uk-form-controls">
                                <select id="form-dbdriver" class="uk-width-1-1 uk-select" name="dbdriver" v-model="config.database.default">
                                    <option v-if="sqlite" value="sqlite">
                                        SQLite
                                    </option>
                                    <option value="mysql">
                                        MySQL
                                    </option>
                                    <option v-if="pgsql" value="pgsql" disabled>
                                        PostgreSQL ({{ 'Soon' | trans }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-margin" v-if="config.database.default === 'pgsql'">
                            <div class="uk-margin">
                                <label for="form-mysql-dbhost" class="uk-form-label">{{ 'Hostname' | trans }}</label>
                                <v-input id="form-mysql-dbhost" view="class: uk-input uk-form-width-large" type="text" name="host" v-model="config.database.connections.pgsql.host" rules="required" message="Host cannot be blank." />
                            </div>
                            <div class="uk-margin">
                                <label for="form-mysql-dbuser" class="uk-form-label">{{ 'User' | trans }}</label>
                                <v-input id="form-mysql-dbuser" view="class: uk-input uk-form-width-large" type="text" name="user" v-model="config.database.connections.pgsql.user" rules="required" message="User cannot be blank." />
                            </div>
                            <div class="uk-margin">
                                <label for="form-mysql-dbpassword" class="uk-form-label">{{ 'Password' | trans }}</label>
                                <v-input id="form-mysql-dbpassword" :type="hidePassword ? 'password' : 'text'" name="password" :view="{type: 'icon', icon: () => hidePassword ? 'lock' : 'unlock', class: 'uk-input uk-form-width-large', containerClass: 'uk-form-controls', iconTag: 'a', iconDir: 'right', iconClick: () => { hidePassword = !hidePassword }}" v-model="config.database.connections.pgsql.password" />
                            </div>
                            <div class="uk-margin">
                                <label for="form-mysql-dbname" class="uk-form-label">{{ 'Database Name' | trans }}</label>
                                <v-input id="form-mysql-dbname" view="class: uk-input uk-form-width-large" type="text" name="dbname" v-model="config.database.connections.pgsql.dbname" rules="required" message="Database name cannot be blank."/>
                            </div>
                            <div class="uk-margin">
                                <label for="form-mysql-dbprefix" class="uk-form-label">{{ 'Table Prefix' | trans }}</label>
                                <v-input id="form-mysql-dbprefix" view="class: uk-input uk-form-width-large" type="text" name="mysqlprefix" v-model="config.database.connections.pgsql.prefix" :rules="{required: true, regex: /^[a-zA-Z][a-zA-Z0-9._\-]*$/}" message="Prefix must start with a letter and can only contain alphanumeric characters (A-Z, 0-9) and underscore (_)" />
                            </div>
                        </div>
                        <div v-if="config.database.default === 'mysql'" class="uk-margin">
                            <div class="uk-margin">
                                <label for="form-mysql-dbhost" class="uk-form-label">{{ 'Hostname' | trans }}</label>
                                <v-input id="form-mysql-dbhost" view="class: uk-input uk-form-width-large" type="text" name="host" v-model="config.database.connections.mysql.host" rules="required" message="Host cannot be blank." />
                            </div>
                            <div class="uk-margin">
                                <label for="form-mysql-dbuser" class="uk-form-label">{{ 'User' | trans }}</label>
                                <v-input id="form-mysql-dbuser" view="class: uk-input uk-form-width-large" type="text" name="user" v-model="config.database.connections.mysql.user" rules="required" message="User cannot be blank." />
                            </div>
                            <div class="uk-margin">
                                <label for="form-mysql-dbpassword" class="uk-form-label">{{ 'Password' | trans }}</label>
                                <v-input id="form-mysql-dbpassword" :type="hidePassword ? 'password' : 'text'" name="password" :view="{type: 'icon', icon: () => hidePassword ? 'lock' : 'unlock', class: 'uk-input uk-form-width-large', containerClass: 'uk-form-controls', iconTag: 'a', iconDir: 'right', iconClick: () => { hidePassword = !hidePassword }}" v-model="config.database.connections.mysql.password" />
                            </div>
                            <div class="uk-margin">
                                <label for="form-mysql-dbname" class="uk-form-label">{{ 'Database Name' | trans }}</label>
                                <v-input id="form-mysql-dbname" view="class: uk-input uk-form-width-large" type="text" name="dbname" v-model="config.database.connections.mysql.dbname" rules="required" message="Database name cannot be blank."/>
                            </div>
                            <div class="uk-margin">
                                <label for="form-mysql-dbprefix" class="uk-form-label">{{ 'Table Prefix' | trans }}</label>
                                <v-input id="form-mysql-dbprefix" view="class: uk-input uk-form-width-large" type="text" name="mysqlprefix" v-model="config.database.connections.mysql.prefix" :rules="{required: true, regex: /^[a-zA-Z][a-zA-Z0-9._\-]*$/}" message="Prefix must start with a letter and can only contain alphanumeric characters (A-Z, 0-9) and underscore (_)" />
                            </div>
                        </div>
                        <div class="uk-margin" v-show="config.database.default == 'sqlite'">
                            <div class="uk-margin">
                                <label for="form-sqlite-dbprefix" class="uk-form-label">{{ 'Table Prefix' | trans }}</label>
                                <v-input id="form-sqlite-dbprefix" view="class: uk-input uk-form-width-large" type="text" name="sqliteprefix" v-model="config.database.connections.sqlite.prefix" :rules="{required: true, regex: /^[a-zA-Z][a-zA-Z0-9._\-]*$/}" message="Prefix must start with a letter and can only contain alphanumeric characters (A-Z, 0-9) and underscore (_)" />
                            </div>
                        </div>
                        <div class="uk-margin uk-text-right uk-margin-remove-bottom">
                            <button class="uk-button uk-button-primary" type="submit" autofocus="">
                                <span class="uk-text-middle">{{ 'Next' | trans }}</span>
                                <span class="uk-margin-small-left" v-html="helper.iconRight"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </template>
            <template v-slot:site="{ step, passes }">
                <div :step="step" class="uk-card uk-card-default uk-card-body">

                    <h1 class="uk-card-title uk-text-center">{{ 'Setup your site' | trans }}</h1>
                    <div class="uk-margin uk-text-muted uk-text-center">{{ 'Choose a title and create the administrator account.' | trans }}</div>

                    <form class="uk-form-horizontal tm-form-horizontal" @submit.prevent="passes(stepSite)">
                        <div class="uk-margin">
                            <label for="form-sitename" class="uk-form-label">{{ 'Site Title' | trans }}</label>
                            <v-input id="form-sitename" view="class: uk-input uk-form-width-large" type="text" name="name" v-model="option['system/site'].title" rules="required" message="Site title cannot be blank." />
                        </div>
                        <div class="uk-margin">
                            <label for="form-username" class="uk-form-label">{{ 'Username' | trans }}</label>
                            <v-input id="form-username" view="class: uk-input uk-form-width-large" type="text" name="user" v-model="user.username" :rules="{required: true, regex: /^[a-zA-Z0-9._\-]{3,}$/}" message='Username cannot be blank and may only contain alphanumeric characters (A-Z, 0-9) and some special characters ("._-")' />
                        </div>
                        <div class="uk-margin">
                            <label for="form-password" class="uk-form-label">{{ 'Password' | trans }}</label>
                            <v-input id="form-password" :type="hidePassword ? 'password' : 'text'" name="password" :view="{type: 'icon', icon: () => hidePassword ? 'lock' : 'unlock', class: 'uk-input uk-form-width-large', containerClass: 'uk-form-controls', iconTag: 'a', iconDir: 'right', iconClick: () => { hidePassword = !hidePassword }}" v-model="user.password" rules="required" message="Password cannot be blank." />
                        </div>
                        <div class="uk-margin">
                            <label for="form-email" class="uk-form-label">{{ 'Email' | trans }}</label>
                            <v-input id="form-email" view="class: uk-input uk-form-width-large" type="email" name="email" v-model="user.email" rules="required|email" message="Field must be a valid email address." />
                        </div>
                        <div>
                            <button class="uk-button uk-button-primary" type="submit" autofocus="">
                                <span class="uk-text-middle">{{ 'Install' | trans }}</span>
                                <span class="uk-margin-small-left" v-html="helper.iconRight"></span>
                            </button>
                        </div>
                    </form>

                </div>
            </template>
            <template v-slot:finish="{ step }">
                <div :step="step">
                    <div class="uk-text-center" v-show="status == 'install'">
                        <svg class="tm-loader" width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
                            <g><circle cx="0" cy="0" r="70" fill="none" stroke-width="2"/></g>
                        </svg>
                    </div>

                    <div class="uk-panel uk-padding-small uk-text-center" v-show="status == 'finished'">
                        <a :href="$url.route('admin')">
                            <svg class="tm-checkmark" width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <polyline fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="5.125,63.25 27.375,89.375 95.25,18.875"/>
                            </svg>
                        </a>
                    </div>

                    <div class="uk-card uk-card-default uk-card-body" v-show="status == 'failed'">
                        <h1>{{ 'Installation failed!' | trans }}</h1>
                        <div class="uk-text-break">{{ message }}</div>
                        <div class="uk-margin uk-text-right uk-margin-remove-bottom">
                            <button type="button" class="uk-button uk-button-primary" @click="stepInstall" autofocus="">{{ 'Retry' | trans }}</button>
                        </div>
                    </div>
                </div>
            </template>
        </installer-steps>
    </div>
</template>

<script>

// eslint-disable-next-line import/no-unresolved
import { ValidationObserver, VInput } from 'SystemApp/components/validation.vue';

// eslint-disable-next-line import/no-mutable-exports
let Installer = {

    name: 'installer',

    el: '#installer',

    data() {
        return _.merge({
            step: 'start',
            status: '',
            message: '',
            config: {
                database: {
                    connections: {
                        mysql: {
                            user: '',
                            host: 'localhost',
                            dbname: 'greencheap',
                            port: 3306,
                        },
                        pgsql: {
                            user: '',
                            host: 'localhost',
                            dbname: 'greencheap',
                            port: 5432,
                        },
                        sqlite: {},
                    },
                    default: '',
                },
            },
            option: { system: { admin: {}, site: {}}, 'system/site': { title: ''} },
            user: { username: 'admin' },
            hidePassword: true,
            editingPassword: false,
            steps: ['start', 'language', 'database', 'site', 'finish'],
            helper: {
                iconRight: `<svg width="18" height="11" viewBox="0 0 18 11" xmlns="http://www.w3.org/2000/svg"><line fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-miterlimit="10" x1="3" y1="5.5" x2="15" y2="5.5"/><path fill="#FFFFFF" d="M10.5,10.9c-0.128,0-0.256-0.049-0.354-0.146c-0.195-0.195-0.195-0.512,0-0.707l4.597-4.597l-4.597-4.597c-0.195-0.195-0.195-0.512,0-0.707s0.512-0.195,0.707,0l4.95,4.95c0.195,0.195,0.195,0.512,0,0.707l-4.95,4.95C10.756,10.852,10.628,10.9,10.5,10.9z"/></svg>`
            }
        }, window.$installer);
    },

    created() {
        // set default db
        this.config.database.default = this.sqlite ? 'sqlite' : 'mysql';
        // setup default table prefix
        _.forEach(this.config.database.connections, connection => { _.extend(connection, { prefix: 'gc_' }) })
    },

    methods: {

        resource(action, body) {
            return this.$http.post(`installer/${action}`, body);
        },

        gotoStep(step) {
            this.$set(this, 'step', step);
            if (step == 'language') this.focuslang();
        },

        focuslang() {
            this.$nextTick(() => {
                document.getElementById('selectbox').focus();
            });
        },

        stepLanguage() {
            this.$asset({ js: [this.$url.route(`system/intl/${this.locale}`)] }).then(function () {
                this.$set(this.option.system.admin, 'locale', this.locale);
                this.$set(this.option.system.site, 'locale', this.locale);
                this.$locale = window.$locale;
                this.gotoStep('database');
            });
        },

        stepDatabase() {
            const config = _.cloneDeep(this.config);

            _.forEach(config.database.connections, (connection, name) => {
                if (name != config.database.default) {
                    delete (config.database.connections[name]);
                } else if (connection.host) {
                    connection.host = connection.host.replace(/:(\d+)$/, (match, port) => {
                        connection.port = port;
                        return '';
                    });
                }
            });

            this.resource('check', { config, locale: this.locale }).then(function (res) {
                let { data } = res;

                if (!this.isPlainObject(data)) {
                    data = { message: 'Whoops, something went wrong' };
                }

                if (data.status == 'no-tables') {
                    this.gotoStep('site');
                    this.config = config;
                } else {
                    this.$set(this, 'status', data.status);
                    this.$set(this, 'message', data.message);
                }
            });
        },

        stepSite() {
            this.gotoStep('finish');
            this.stepInstall();
        },

        stepInstall() {
            const vm = this;

            this.$set(this, 'status', 'install');

            this.resource('install', {
                config: this.config, option: this.option, user: this.user, locale: this.locale,
            }).then(function (res) {
                let { data } = res;

                setTimeout(() => {
                    if (!vm.isPlainObject(data)) {
                        data = { message: 'Whoops, something went wrong' };
                    }

                    if (data.status == 'success') {
                        this.$set(this, 'status', 'finished');

                        // redirect to login after 3s
                        setTimeout(() => {
                            location.href = this.$url.route('admin');
                        }, 3000);
                    } else {
                        this.$set(this, 'status', 'failed');
                        this.$set(this, 'message', data.message);
                    }
                }, 2000);
            });
        },

        isPlainObject(o) {
            return !!o && typeof o === 'object' && Object.prototype.toString.call(o) === '[object Object]';
        },
    },

    components: {

        'installer-steps': {
            props: ['current', 'steps'],
            template: `
                <validation-observer tag="div" class="tm-container" v-slot="{ invalid, passes }">
                    <template v-for="(step, index) in steps">
                        <transition name="slide">
                            <slot :name="step" :step="step" :invalid="invalid" :passes="passes" v-if="step === current" />
                        </transition>
                    </template>
                </validation-observer>`,
            components: {
                ValidationObserver
            }
        },
        VInput
    },

};

export default Installer;

Vue.ready(Installer);

</script>
