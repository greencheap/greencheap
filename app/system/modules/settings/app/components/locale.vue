<template>
    <div>
        <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
            <div>
                <h2 class="uk-margin-remove">
                    {{ 'Localization' | trans }}
                </h2>
            </div>
            <div class="uk-margin-small">
                <button class="uk-button uk-button-primary" type="submit">
                    {{ 'Save' | trans }}
                </button>
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-sitelocale" class="uk-form-label">{{ 'Site Locale' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-sitelocale" v-model="option.site.locale" class="uk-form-width-large uk-select">
                    <option v-for="(lang, key) in locales" :key="key" :value="key">
                        {{ lang }}
                    </option>
                </select>
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-adminlocale" class="uk-form-label">{{ 'Admin Locale' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-adminlocale" v-model="option.admin.locale" class="uk-form-width-large uk-select">
                    <option v-for="(lang, key) in locales" :key="key" :value="key">
                        {{ lang }}
                    </option>
                </select>
            </div>
        </div>

        <p v-html="$trans('Is your language not available? Please help out by translating GreenCheap into your own language on %link%.', {link:link})" />
    </div>
</template>

<script>

export default {

    section: {
        label: 'Localization',
        icon: 'world',
        priority: 20,
    },

    props: ['config', 'options'],

    data() {
        return { locales: window.$system.locales };
    },

    computed: {

        link() {
            return '<a href="https://www.transifex.com/greencheap/greencheap-cms/">Transifex</a>';
        },

        option() {
            return this.$root.options.system;
        },

    },

};

</script>
