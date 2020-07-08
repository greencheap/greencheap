<template>

    <v-modal ref="modal" :closed="close">
        <form class=" uk-form-stacked" @submit.prevent="update">

            <div class="uk-modal-header">
                <h2>{{ 'Add Image' | trans }}</h2>
            </div>

            <div class="uk-modal-body">
                <div class="uk-margin">
                    <input-image @image:selected="selected" v-model="image.data.src" input-class="uk-width-1-1"></input-image>
                </div>

                <div class="uk-margin">
                    <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-src" class="uk-width-1-1 uk-input" type="text" v-model="image.data.src" lazy>
                    </div>
                </div>

                <div class="uk-margin">
                    <label for="form-alt" class="uk-form-label">{{ 'Alt' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-alt" class="uk-width-1-1 uk-input" type="text" v-model="image.data.alt">
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

    name: 'image-picker',

    data: function () {
        return {
            image: {data: {src: '', alt: ''}}
        }
    },

    mounted: function () {

        this.$refs.modal.open();

    },

    methods: {
        selected: function(path){

            if (path && !this.image.data.alt) {

                var alt   = path.split('/').slice(-1)[0].replace(/\.(jpeg|jpg|png|svg|gif)$/i, '').replace(/(_|-)/g, ' ').trim(),
                    first = alt.charAt(0).toUpperCase();

                this.image.data.alt = first + alt.substr(1);
            }

        },

        close: function() {
            this.$destroy(true);
        },

        update: function () {
            // Changed Order
            this.$emit('select', this.image);
            this.$refs.modal.close();

        }

    }

};

</script>
