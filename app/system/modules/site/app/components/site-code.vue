<template>
    <div>
        <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
            <div>
                <h2 class="uk-h3 uk-margin-remove">
                    {{ 'Code' | trans }}
                </h2>
            </div>
            <div class="uk-margin-small">
                <button class="uk-button uk-button-primary" type="submit">
                    {{ 'Save' | trans }}
                </button>
            </div>
        </div>

        <p>{{ 'Insert code in the header and footer of your theme. This is useful for tracking codes and meta tags.' | trans }}</p>

        <div class="uk-form-stacked">
            <div class="uk-margin">
                <label for="form-codeheader" class="uk-form-label">{{ 'Header' | trans }}</label>
                <div class="uk-form-controls">
                    <v-editor v-model="config.code.header" type="code" />
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-codeheader" class="uk-form-label">{{ 'Footer' | trans }}</label>
                <div class="uk-form-controls">
                    <v-editor v-model="config.code.footer" type="code" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {

    section: {
        label: 'Code',
        icon: 'pk-icon-large-code',
        priority: 20,
    },

    props: ['config'],

};

</script>
