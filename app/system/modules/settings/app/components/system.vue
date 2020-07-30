<template>
    <div>
        <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
            <div>
                <h2 class="uk-margin-remove">
                    {{ 'System' | trans }}
                </h2>
            </div>
            <div class="uk-margin-small">
                <button class="uk-button uk-button-primary" type="submit">
                    {{ 'Save' | trans }}
                </button>
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-storage" class="uk-form-label">{{ 'Storage' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-storage" v-model="$root.config['system-finder'].storage" class="uk-form-width-large uk-input" type="text" placeholder="/storage">
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-fileextensions" class="uk-form-label">{{ 'File Extensions' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-fileextensions" v-model="$root.options['system-finder']['extensions']" class="uk-form-width-large uk-input" type="text">
                <div class="uk-text-meta">
                    {{ 'Allowed file extensions for the storage upload.' | trans }}
                </div>
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-user-recaptcha-enable" class="uk-form-label">{{ 'Google reCAPTCHA' | trans }}</label>
            <div class="uk-form-controls uk-form-controls-text">
                <div>
                    <label><input id="form-user-recaptcha-enable" v-model="$root.options['system-captcha'].recaptcha_enable" class="uk-checkbox" type="checkbox"><span class="uk-margin-small-left">{{ 'Enable for user registration and comments' | trans }}</span></label>
                </div>
                <div v-if="$root.options['system-captcha'].recaptcha_enable" class="uk-margin-small">
                    <input id="form-user-recaptcha-sitekey" v-model="$root.options['system-captcha'].recaptcha_sitekey" class="uk-form-width-large uk-input" :placeholder="'Site key' | trans">
                </div>
                <div v-if="$root.options['system-captcha'].recaptcha_enable" class="uk-margin-small">
                    <input id="form-user-recaptcha-secret" v-model="$root.options['system-captcha'].recaptcha_secret" class="uk-form-width-large uk-input" :placeholder="'Secret key' | trans">
                </div>
                <div class="uk-text-meta">
                    {{ 'Only key pairs for Google reRECAPTCHA V2 Invisible are supported.' | trans }}
                </div>
            </div>
        </div>


        <div class="uk-margin">
            <label class="uk-form-label">{{ 'Developer' | trans }}</label>
            <div class="uk-form-controls uk-form-controls-text">
                <div class="uk-margin-small">
                    <label><input v-model="$root.config.application.beta" class="uk-checkbox" type="checkbox" value="1"><span class="uk-margin-small-left">{{ 'Enable beta version mode' | trans }}</span></label>
                </div>
                <div class="uk-margin-small">
                    <label><input v-model="$root.config.application.debug" class="uk-checkbox" type="checkbox" value="1"><span class="uk-margin-small-left">{{ 'Enable debug mode' | trans }}</span></label>
                </div>
                <div class="uk-margin-small">
                    <label><input v-model="$root.config.debug.enabled" class="uk-checkbox" type="checkbox" value="1" :disabled="!sqlite"><span class="uk-margin-small-left">{{ 'Enable debug toolbar' | trans }}</span></label>
                </div>
                <div v-if="!sqlite" class="uk-text-meta">
                    {{ 'Please enable the SQLite database extension.' | trans }}
                </div>
                <div v-if="$root.config.application.debug || $root.config.debug.enabled" class="uk-text-meta">
                    {{ 'Please note that enabling debug mode or toolbar has serious security implications.' | trans }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {

    section: {
        label: 'System',
        icon: 'pk-icon-large-settings',
        priority: 10,
    },

    // eslint-disable-next-line vue/require-prop-types
    props: ['config', 'options'],

    data() {
        return window.$system;
    },
};

</script>
