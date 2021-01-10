<template>
    <div>
        <a v-if="!img_meta.src" class="uk-placeholder uk-link-reset uk-text-center uk-display-block uk-margin-remove" @click.prevent="pick">
            <span class="uk-display-block uk-text-primary" uk-icon="icon:image-alt;ratio:3"></span>
            <p class="uk-text-muted uk-margin-small-top">{{ 'Add Image' | trans }}</p>
        </a>

        <div v-else :class="['uk-position-relative uk-transition-toggle uk-visible-toggle', cls ? cls : '']">
            <img :src="$url(img_meta.src)">

            <div class="uk-transition-fade uk-position-cover pk-thumbnail-overlay uk-flex uk-flex-center uk-flex-middle" />

            <a class="uk-position-cover" @click.prevent="pick" />

            <div class="uk-card-badge pk-panel-badge uk-invisible-hover">
                <ul class="uk-subnav pk-subnav-icon">
                    <li>
                        <a
                            v-confirm="'Reset image?'"
                            class="uk-icon-link"
                            uk-icon="icon: trash"
                            :title="'Delete' | trans"
                            uk-tooltip="delay: 500"
                            @click.prevent="remove"
                        />
                    </li>
                </ul>
            </div>
        </div>

        <v-modal ref="modal">
            <form class="uk-form-stacked" @submit="update">
                <div class="uk-modal-header">
                    <h2>{{ 'Image' | trans }}</h2>
                </div>

                <div class="uk-modal-body">
                    <div class="uk-margin">
                        <input-image v-model="img.src" :input-field="false" input-class="uk-form-width-large" />
                    </div>

                    <div class="uk-margin">
                        <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                        <div class="uk-form-controls">
                            <input id="form-src" v-model.lazy="img.src" class="uk-width-1-1 uk-input" type="text">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="form-alt" class="uk-form-label">{{ 'Alt' | trans }}</label>
                        <div class="uk-form-controls">
                            <input id="form-alt" v-model="img.alt" class="uk-width-1-1 uk-input" type="text">
                        </div>
                    </div>
                </div>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">
                        {{ 'Cancel' | trans }}
                    </button>
                    <button class="uk-button uk-button-primary" type="button" @click.prevent="update">
                        {{ 'Update' | trans }}
                    </button>
                </div>
            </form>
        </v-modal>
    </div>
</template>

<script>

export default {

    props: {
        cls: {
            type: String,
            default: '',
        },
        image: Object,
    },

    data() {
        return _.merge({
            img: {},
            img_meta: {},
        }, $greencheap);
    },

    mounted() {
        this.$set(this, 'img_meta', this.image || { src: '', alt: '' });
        this.$set(this, 'img', _.extend({}, this.img_meta));

        this.$on('image:selected', function (path) {
            if (path && !this.img.alt) {
                const alt = path.split('/').slice(-1)[0].replace(/\.(jpeg|jpg|png|svg|gif)$/i, '').replace(/(_|-)/g, ' ').trim();
                const first = alt.charAt(0).toUpperCase();

                this.img.alt = first + alt.substr(1);
            }
        });
    },

    methods: {

        pick() {
            this.img.src = this.img_meta.src;
            this.img.alt = this.img_meta.alt;
            this.$refs.modal.open();
        },

        update() {
            this.img_meta.src = this.img.src;
            this.img_meta.alt = this.img.alt;
            this.$emit('input', this.img_meta);
            this.$refs.modal.close();
        },

        remove() {
            this.img.src = '';
            this.img_meta.src = '';
        },
    },

};

Vue.component('input-image-meta', (resolve, reject) => {
    Vue.asset({
        js: [
            'app/system/modules/finder/app/bundle/panel-finder.js',
        ],
    }).then(() => {
        resolve(require('./input-image-meta.vue'));
    });
});

</script>
