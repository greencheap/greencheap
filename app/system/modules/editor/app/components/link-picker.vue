<template>

    <v-modal ref="modal" :closed="close">
        <form class="uk-form-stacked" @submit.prevent="update">

            <div class="uk-modal-header">
                <h2>{{ 'Add Link' | trans }}</h2>
            </div>

            <div class="uk-modal-body">
                <div class="uk-margin">
                    <label for="form-link-title" class="uk-form-label">{{ 'Title' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-link-title" class="uk-input uk-width-1-1" type="text" v-model="link.txt">
                    </div>
                </div>

                <div class="uk-margin">
                    <label for="form-link-url" class="uk-form-label">{{ 'Url' | trans }}</label>
                    <div class="uk-form-controls">
                        <input-link id="form-link-url" input-class="uk-width-xlarge" v-model="link.link"></input-link>
                    </div>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-primary" type="submit">{{ 'Update' | trans }}</button>
            </div>

        </form>
    </v-modal>

</template>

<script>

export default {

    data: function () {
        return {
            link: {link: '', txt: '', class: ''}
        }
    },

    mounted: function () {
        this.$refs.modal.open();
    },

    methods: {

        close: function() {
            this.$destroy(true);
        },

        update: function () {
            this.$refs.modal.close();
            this.$emit('select', this.link);
        }

    }

};

</script>
