<template>
    <div>
        <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
            <div>
                <v-title :title="'Mail' | trans" />
            </div>
            <div class="uk-margin-small">
                <button class="uk-button uk-button-primary" type="submit">
                    {{ "Save" | trans }}
                </button>
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-emailaddress" class="uk-form-label">{{ "From Email" | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-emailaddress" v-model="options.from_address" class="uk-form-width-large uk-input" type="text" />
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-fromname" class="uk-form-label">{{ "From Name" | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-fromname" v-model="options.from_name" class="uk-form-width-large uk-input" type="text" />
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-mailer" class="uk-form-label">{{ "Mailer" | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-mailer" v-model="options.driver" class="uk-form-width-large uk-select">
                    <option value="mail">
                        {{ "PHP Mailer" | trans }}
                    </option>
                    <option value="smtp">
                        {{ "SMTP Mailer" | trans }}
                    </option>
                </select>
            </div>
        </div>

        <div v-show="'smtp' == options.driver" class="uk-margin">
            <div class="uk-margin">
                <label for="form-smtpport" class="uk-form-label">{{ "SMTP Port" | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-smtpport" v-model="options.port" class="uk-form-width-large uk-input" type="text" />
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-smtphost" class="uk-form-label">{{ "SMTP Host" | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-smtphost" v-model="options.host" class="uk-form-width-large uk-input" type="text" />
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-smtpuser" class="uk-form-label">{{ "SMTP User" | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-smtpuser" v-model="options.username" class="uk-form-width-large uk-input" type="text" />
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-smtppassword" class="uk-form-label">{{ "SMTP Password" | trans }}</label>
                <div class="uk-form-controls js-password">
                    <div class="uk-form-password">
                        <input id="form-smtppassword" v-model="options.password" class="uk-form-width-large uk-input" :type="hidePassword ? 'password' : 'text'" />
                        <a href="#" class="uk-form-password-toggle uk-text-small uk-display-block" @click.prevent="hidePassword = !hidePassword">{{ hidePassword ? "Show" : "Hide" | trans }}</a>
                    </div>
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-smtpencryption" class="uk-form-label">{{ "SMTP Encryption" | trans }}</label>
                <div class="uk-form-controls">
                    <select id="form-smtpencryption" v-model="options.encryption" class="uk-form-width-large uk-select">
                        <option value="" :selected="!options.encrytion">
                            {{ "None" | trans }}
                        </option>
                        <option value="ssl" :disabled="!ssl">
                            {{ "SSL" | trans }}
                        </option>
                        <option value="tls" :disabled="!ssl">
                            {{ "TLS" | trans }}
                        </option>
                    </select>
                    <p v-if="!ssl" class="uk-margin-small-top uk-text-meta">
                        {{ "Please enable the PHP Open SSL extension." | trans }}
                    </p>
                </div>
            </div>
        </div>

        <div class="uk-margin">
            <div class="uk-form-controls">
                <button v-show="'smtp' == options.driver" class="uk-button uk-button-default uk-text-truncate" type="button" @click="test('smtp')">
                    {{ "Check Connection" | trans }}
                </button>
                <button class="uk-button uk-button-default uk-text-truncate" type="button" @click="test('email')">
                    {{ "Send Test Email" | trans }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
var Mail = {
    mixins: [Theme.Mixins.Helper],

    section: {
        label: "Mail",
        icon: "mail",
        priority: 40,
    },

    props: ["config", "options"],

    data() {
        return _.extend(
            {
                hidePassword: true,
                processing: {
                    smtp: false,
                    email: false,
                },
            },
            window.$mail
        );
    },

    methods: {
        test(driver) {
            this.processing[driver] = true;
            if (driver === "email") this.processing.email = true;
            this.$http
                .post(`admin/system/${driver}`, { option: this.options })
                .then(
                    function (res) {
                        const { data } = res;
                        this.$notify(data.message, data.success ? "" : "danger");
                    },
                    function () {
                        this.$notify("Ajax request to server failed.", "danger");
                    }
                )
                .then(function () {
                    setTimeout(() => {
                        this.processing[driver] = false;
                    }, 200);
                });
        },
    },
};

export default Mail;

window.Settings.components["system-mail"] = Mail;
</script>
