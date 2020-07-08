<template>
    <div>
        <a v-if="!source" class="uk-placeholder uk-text-center uk-display-block uk-margin-remove" @click.prevent="pick">
            <img width="60" height="60" :alt="'Placeholder Image' | trans" :src="$url('app/system/assets/images/placeholder-video.svg')">
            <p class="uk-text-muted uk-margin-small-top">{{ 'Select Video' | trans }}</p>
        </a>

        <div v-else :class="getClass()">
            <img v-if="image" :src="image">
            <video v-if="video" controls class="uk-width-1-1" :src="video" uk-video="autoplay: false" />

            <div class="uk-card-badge pk-panel-badge uk-invisible-hover">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="uk-icon-link" uk-icon="icon: file-edit" :title="'Edit' | trans" uk-tooltip="delay: 500" @click.prevent="pick" /></li>
                    <li>
                        <a
                            v-confirm="'Reset video?'"
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

        <v-modal ref="modal" large>
            <panel-finder ref="finder" :root="storage" :modal="true" @select:finder="selectFinder" />

            <div class="uk-modal-footer">
                <div class="uk-flex uk-flex-middle uk-flex-between">
                    <div>
                        <div v-if="isFinder">
                            <span v-if="!finder.selected.length" class="uk-text-meta">{{ '{0} %count% Files|{1} %count% File|]1,Inf[ %count% Files' | transChoice(finder.count, {count: finder.count}) }}</span>
                            <span v-else class="uk-text-meta">{{ '{1} %count% File selected|]1,Inf[ %count% Files selected' | transChoice(finder.selected.length, {count:finder.selected.length}) }}</span>
                        </div>
                    </div>
                    <div>
                        <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">
                            {{ 'Cancel' | trans }}
                        </button>
                        <button class="uk-button uk-button-primary" type="button" :disabled="!choice" @click.prevent="select">
                            {{ 'Select' | trans }}
                        </button>
                    </div>
                </div>
            </div>
        </v-modal>
    </div>
</template>

<script>

export default {

    props: ['source'],

    data() {
        return _.merge({
            image: undefined,
            video: undefined,
            choice: '',
            finder: {},
        }, $greencheap);
    },

    computed: {

        isFinder() {
            return !!((this.finder.hasOwnProperty('selected') && this.finder.selected));
        },

    },

    mounted() {
        const vm = this;
        UIkit.util.on(this.$refs.modal.$el, 'shown', () => {
            vm.finder = vm.$refs.finder;
        });
    },

    watch: {
        source: {
            handler: 'update',
            immediate: true,
        },
    },

    methods: {
        selectFinder(val) {
            this.choice = this.selectButton();
        },

        selectButton() {
            if (!this.isFinder) return;
            const selected = this.$refs.finder.getSelected();
            return selected && selected.length === 1 && this.$refs.finder.isVideo(selected[0]);
        },

        getClass() {
            return `uk-form-width-large uk-inline-clip uk-transition-toggle uk-visible-toggle ${this.class}`;
        },

        pick() {
            this.$refs.modal.open();
        },

        select() {
            const source = decodeURI(this.$refs.finder.getSelected()[0]);
            this.$emit('input', source);
            this.$refs.finder.removeSelection();
            this.$refs.modal.close();
        },

        remove() {
            // this.source = '';
            this.$emit('input', '');
        },

        update(src) {
            let matches;

            this.$set(this, 'image', undefined);
            this.$set(this, 'video', undefined);

            if (matches = (src.match(/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/))) {
                this.image = `//img.youtube.com/vi/${matches[1]}/hqdefault.jpg`;
            } else if (src.match(/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/)) {
                this.$http.get('http://vimeo.com/api/oembed.json', { params: { url: src }, cache: 10 }).then(function (res) {
                    const { data } = res;
                    this.image = data.thumbnail_url;
                });
            } else {
                this.video = this.$url(src);
            }
        },

    },

};

Vue.component('input-video', (resolve, reject) => {
    Vue.asset({
        js: [
            'app/system/modules/finder/app/bundle/panel-finder.js',
        ],
    }).then(() => {
        resolve(require('./input-video.vue'));
    });
});

</script>
