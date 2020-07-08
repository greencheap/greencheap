<template>

        <v-modal ref="modal" :closed="close">
            <form class=" uk-form-stacked" @submit.prevent="update">

                <div class="uk-modal-header">
                    <h2>{{ 'Add Video' | trans }}</h2>
                </div>

                <div class="uk-modal-body">
                    <div class="uk-margin">
                        <input-video :source.sync="video.data.src" v-model="video.data.src"></input-video>
                    </div>

                    <div class="uk-margin">
                        <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                        <div class="uk-form-controls">
                            <input id="form-src" class="uk-width-1-1 uk-input" type="text" v-model="video.data.src" debounce="500">
                        </div>
                    </div>
                    <div class="uk-child-width-1-2@s uk-grid-small uk-flex-bottom" uk-grid>
                        <div>
                            <div class="uk-child-width-1-2 uk-grid-small" uk-grid>
                                <div>
                                    <label for="form-src" class="uk-form-label">{{ 'Width' | trans }}</label>
                                    <input class="uk-width-1-1 uk-input" id="form-width" type="text" :placeholder="'auto' | trans" v-model="video.data.width">
                                </div>
                                <div>
                                    <label for="form-src" class="uk-form-label">{{ 'Height' | trans }}</label>
                                    <input class="uk-width-1-1 uk-input" id="form-height" type="text" :disabled="!isVimeo && !isYoutube" :placeholder="'auto' | trans" v-model="video.data.height">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="uk-child-width-1-2 uk-grid-small uk-text-small" uk-grid>
                                <div class="uk-text-nowrap uk-text-truncate">
                                    <label><input class="uk-checkbox" type="checkbox" v-model="video.data.autoplay"><span class="uk-margin-small-left">{{ 'Autoplay' | trans }}</span></label><br>
                                    <label v-show="!isVimeo"><input class="uk-checkbox" type="checkbox" v-model="video.data.controls"><span class="uk-margin-small-left">{{ 'Controls' | trans }}</span></label>
                                </div>
                                <div class="uk-text-nowrap uk-text-truncate">
                                    <label><input class="uk-checkbox" type="checkbox" v-model="video.data.loop"><span class="uk-margin-small-left">{{ 'Loop' | trans }}</span></label><br>
                                    <label v-show="!isVimeo && !isYoutube"><input class="uk-checkbox" type="checkbox" v-model="video.data.muted"><span class="uk-margin-small-left">{{ 'Muted' | trans }}</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-margin" v-show="!isYoutube && !isVimeo">
                        <label class="uk-form-label">{{ 'Poster Image' | trans }}</label>
                        <div class="uk-form-controls">
                            <input-image v-model="video.data.poster" :input-field="false" input-class="uk-form-width-large"></input-image>
                        </div>
                    </div>
                </div>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                    <button class="uk-button uk-button-primary" type="submit" :disabled="!video.data.src">{{ 'Update' | trans }}</button>
                </div>

            </form>

        </v-modal>

</template>

<script>

export default {

    name: 'video-picker',

    data: function () {
        return {
            video: {data: {src: '', controls: true}}
        }
    },

    mounted: function () {
        this.$refs.modal.open();
    },

    computed: {

        isYoutube: function () {
            return this.video.data.src ? this.video.data.src.match(/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/) : false;
        },

        isVimeo: function () {
            return this.video.data.src ? this.video.data.src.match(/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/) : false;
        }

    },

    methods: {

        close: function() {
            this.$destroy(true);
        },

        update: function () {
            this.$emit('select', this.video);
            this.$refs.modal.close();
        }

    }

};

</script>
