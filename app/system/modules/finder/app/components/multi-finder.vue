<template>
    <div>
        <a v-if="!img_meta.src" class="uk-placeholder uk-link-reset uk-text-center uk-display-block uk-margin-remove" @click.prevent="pick">
            <span class="uk-display-block uk-text-primary" uk-icon="icon:image;ratio:3"></span>
            <p class="uk-text-muted uk-margin-small-top">{{ "Add Image" | trans }}</p>
        </a>

        <div v-else :class="['uk-position-relative uk-transition-toggle uk-visible-toggle', cls ? cls : '']">
            <img :src="$url(img_meta.src)" />

            <div class="uk-transition-fade uk-position-cover pk-thumbnail-overlay uk-flex uk-flex-center uk-flex-middle" />

            <a class="uk-position-cover" @click.prevent="pick" />

            <div class="uk-card-badge pk-panel-badge uk-invisible-hover">
                <ul class="uk-subnav pk-subnav-icon">
                    <li>
                        <a v-confirm="'Reset image?'" class="uk-icon-link" uk-icon="icon: trash" :title="'Delete' | trans" uk-tooltip="delay: 500" @click.prevent="remove" />
                    </li>
                </ul>
            </div>
        </div>

        <v-modal ref="modal">
            <form class="uk-form-stacked" @submit="update">
                <div class="uk-modal-header">
                    <h2>{{ "Image" | trans }}</h2>
                </div>

                <div class="uk-modal-body">
                    <div class="uk-margin">
                        <div v-if="isLoad" class="uk-height-small uk-flex uk-flex-center uk-flex-middle uk-width-expand">
                            <span uk-spinner></span>
                        </div>
                        <input-image v-else v-model="img.src" :input-field="false" input-class="uk-width-expand" />
                    </div>

                    <ul v-if="multiFinders.length" class="uk-grid-small" uk-grid>
                        <li v-for="(multiFinder, id) in multiFinders" :key="id">
                            <button type="button" @click.prevent="pickMultiFinder(multiFinder)" class="uk-button uk-button-default uk-width-expand">
                                <span class="uk-margin-small-right uk-icon uk-icon-image" :data-src="$url(multiFinder.icon)" uk-img></span>
                                {{ multiFinder.label }}
                            </button>
                        </li>
                    </ul>

                    <v-modal ref="multiFinderModal" :options="{ bgClose: false }" large>
                        <div class="uk-modal-header">
                            <h4><span class="uk-margin-small-right uk-icon uk-icon-image" :data-src="$url(multiFinder.icon)" uk-img></span> {{ multiFinder.label }}</h4>
                        </div>
                        <div class="uk-modal-body">
                            <component :is="multiFinder.component" :source.sync="multiFinder.img_meta" :selected.sync="multiFinder.selected" :count.sync="multiFinder.count"></component>
                        </div>
                        <div v-if="multiFinder" class="uk-modal-footer">
                            <div class="uk-flex uk-flex-middle uk-flex-between">
                                <div>
                                    <div>
                                        <span v-if="!multiFinder.selected.length" class="uk-text-meta">{{ "{1} %count% File|]1,Inf[ %count% Files" | transChoice(multiFinder.count, { count: multiFinder.count }) }}</span>
                                        <span v-else class="uk-text-meta">{{ "{1} %count% File selected|]1,Inf[ %count% Files selected" | transChoice(multiFinder.selected.length, { count: multiFinder.selected.length }) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">
                                        {{ "Cancel" | trans }}
                                    </button>
                                    <button @click.prevent="updateMultiFinderImage" class="uk-button uk-button-primary" :disabled="!multiFinder.img_meta.src" type="button">
                                        {{ "Select" | trans }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </v-modal>

                    <div class="uk-margin">
                        <label for="form-src" class="uk-form-label">{{ "URL" | trans }}</label>
                        <div class="uk-form-controls">
                            <input id="form-src" v-model.lazy="img.src" class="uk-width-1-1 uk-input" type="text" />
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="form-alt" class="uk-form-label">{{ "Alt" | trans }}</label>
                        <div class="uk-form-controls">
                            <input id="form-alt" v-model="img.alt" class="uk-width-1-1 uk-input" type="text" />
                        </div>
                    </div>
                </div>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">
                        {{ "Cancel" | trans }}
                    </button>
                    <button class="uk-button uk-button-primary" type="button" @click.prevent="update">
                        {{ "Update" | trans }}
                    </button>
                </div>
            </form>
        </v-modal>
    </div>
</template>

<script>
const MultiFinder = {
    props: {
        cls: {
            type: String,
            default: "",
        },
        image: Object,
    },

    data() {
        return _.merge(
            {
                img: {},
                img_meta: {},
                multiFinders: [],
                multiFinder: false,
                multiFinderModal: false,
                isLoad: false,
            },
            $greencheap
        );
    },

    created() {
        const multiFinders = [];
        _.forIn(this.$options.components, (component, name) => {
            if (component.multiFinder) {
                multiFinders.push(_.extend({ name, priority: 0, icon: false }, component.multiFinder));
            }
        });
        this.$set(this, "multiFinders", _.sortBy(multiFinders, "priority"));
        this.activeFinder = multiFinders[0];
    },

    mounted() {
        this.$set(this, "img_meta", this.image || { src: "", alt: "" });
        this.$set(this, "img", _.extend({}, this.img_meta));

        this.$on("image:selected", function (path) {
            if (path && !this.img.alt) {
                const alt = path
                    .split("/")
                    .slice(-1)[0]
                    .replace(/\.(jpeg|jpg|png|svg|gif)$/i, "")
                    .replace(/(_|-)/g, " ")
                    .trim();
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
            this.$emit("input", this.img_meta);
            this.$refs.modal.close();
        },

        remove() {
            this.img.src = "";
            this.img_meta.src = "";
        },

        pickMultiFinder(component) {
            this.multiFinder = {
                component: component.name,
                label: component.label,
                icon: component.icon,
                img_meta: {
                    src: "",
                    alt: "",
                },
                selected: [],
                count: 0,
            };
            this.$refs.multiFinderModal.open();
        },

        updateMultiFinderImage() {
            this.startLoad();
            this.img.src = this.multiFinder.img_meta.src;
            this.img.alt = this.multiFinder.img_meta.alt;
            this.img_meta.src = this.multiFinder.img_meta.src;
            this.img_meta.alt = this.multiFinder.img_meta.alt;
            this.$emit("input", this.img_meta);
            this.$refs.multiFinderModal.close();
        },

        startLoad() {
            this.isLoad = true;
            setTimeout(() => (this.isLoad = false), 500);
        },
    },

    components: {},
};

export default MultiFinder;

window.MultiFinder = MultiFinder;

Vue.component("v-multi-finder", function (resolve) {
    Vue.asset({
        js: ["app/system/modules/finder/app/bundle/panel-finder.js"],
    }).then(() => {
        resolve(require("./multi-finder.vue"));
    });
});
</script>
