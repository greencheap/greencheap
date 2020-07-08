<template>
    <div>
        <div class="uk-margin">
            <label for="form-link-file" class="uk-form-label">{{ 'File' | trans }}</label>
            <div class="uk-form-controls">
                <div class="pk-form-link uk-width-1-1">
                    <input id="form-link-file" ref="input" v-model.lazy="file" class="uk-width-1-1 uk-input" type="text">
                    <a class="pk-form-link-toggle pk-link-icon uk-flex-middle" @click.prevent="pick">{{ 'Select' | trans }} <i class="pk-icon-link pk-icon-hover uk-margin-small-left" /></a>
                </div>
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

var LinkStorage = {

    link: {
        label: 'Storage',
    },

    props: ['link'],

    data() {
        return _.merge({
            file: undefined,
            choice: '',
            finder: {},
        }, $greencheap);
    },

    computed: {
        isFinder() {
            return !!((this.finder.hasOwnProperty('selected') && this.finder.selected));
        },
    },

    created() {
        this.assets = this.$asset({
            js: [
                'app/system/modules/finder/app/bundle/panel-finder.js',
            ],
        }).then(function () {
            this.file = '';
        });
    },

    mounted() {
        const vm = this;
        UIkit.util.on(this.$refs.modal.$el, 'shown', () => {
            vm.finder = vm.$refs.finder;
        });
    },

    watch: {
        file(file) {
            this.$parent.link = file;
        },
    },

    methods: {
        selectFinder() {
            this.choice = this.hasSelection();
        },

        pick() {
            this.assets.then(function () {
                this.$refs.modal.open();
            });
        },

        select() {
            this.file = this.$refs.finder.getSelected()[0];
            this.$refs.finder.removeSelection();
            this.$refs.modal.close();
        },

        hasSelection() {
            const selected = this.$refs.finder.getSelected();
            return selected.length === 1;
        },

    },

};

export default LinkStorage;

window.Links.default.components['link-storage'] = LinkStorage;

</script>
