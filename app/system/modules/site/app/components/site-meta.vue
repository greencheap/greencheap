<template>
    <div>
        <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
            <div>
                <h2 class="uk-h3 uk-margin-remove">
                    {{ 'Meta' | trans }}
                </h2>
            </div>
            <div class="uk-margin-small">
                <button class="uk-button uk-button-primary" type="submit">
                    {{ 'Save' | trans }}
                </button>
            </div>
        </div>

        <div class="uk-form-horizontal">
            <div class="uk-margin">
                <label for="form-meta-description" class="uk-form-label">{{ 'Description' | trans }}</label>
                <div class="uk-form-controls">
                    <textarea id="form-meta-description" v-model="config.meta.description" class="uk-form-width-large uk-textarea" rows="5" />
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-meta-image" class="uk-form-label">{{ 'Image' | trans }}</label>
                <div class="uk-form-controls">
                    <input-image v-model="config.meta.image" :source.sync="config.meta.image" input-class="uk-form-width-large" />
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-meta-facebook" class="uk-form-label">Facebook</label>
                <div class="uk-form-controls">
                    <input id="form-meta-facebook" v-model="config.meta.facebook" type="text" class="uk-form-width-large uk-input" placeholder="app_id">
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-meta-twitter" class="uk-form-label">Twitter</label>
                <div class="uk-form-controls">
                    <input id="form-meta-twitter" v-model="config.meta.twitter" type="text" class="uk-form-width-large uk-input" placeholder="@username">
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {

    section: {
        label: 'Meta',
        icon: 'pk-icon-large-meta',
        priority: 50,
    },

    props: ['config'],

};

</script>
