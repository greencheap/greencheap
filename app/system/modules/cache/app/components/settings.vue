<template>
    <div>
        <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
            <div>
                <v-title :title="'Cache' | trans" />
            </div>
            <div class="uk-margin-small">
                <button class="uk-button uk-button-primary" type="submit">
                    {{ "Save" | trans }}
                </button>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label">{{ "Cache" | trans }}</label>
            <div class="uk-form-controls uk-form-controls-text">
                <p v-for="(cache, key) in caches" :key="key" class="uk-margin-small">
                    <label>
                        <input v-model="config.caches.cache.storage" class="uk-radio" type="radio" :value="key" :disabled="!cache.supported" />
                        <span class="uk-margin-small-left">{{ cache.name }}</span>
                    </label>
                </p>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label">{{ "Developer" | trans }}</label>
            <div class="uk-form-controls uk-form-controls-text">
                <p class="uk-margin-small">
                    <label
                        ><input v-model="config.nocache" class="uk-checkbox" type="checkbox" value="1" /><span class="uk-margin-small-left">{{ "Disable cache" | trans }}</span></label
                    >
                </p>
                <p>
                    <button class="uk-button uk-button-primary" type="button" @click.prevent="open">
                        {{ "Clear Cache" | trans }}
                    </button>
                </p>
            </div>
        </div>

        <v-modal ref="modal">
            <form class="uk-form-stacked">
                <div class="uk-modal-header">
                    <h2>{{ "Select Cache to Clear" | trans }}</h2>
                </div>

                <div class="uk-modal-body">
                    <div class="uk-margin">
                        <p class="uk-margin-small">
                            <label
                                ><input v-model="cache.cache" class="uk-checkbox" type="checkbox" /><span class="uk-margin-small-left">{{ "System Cache" | trans }}</span></label
                            >
                        </p>

                        <p class="uk-margin-small">
                            <label
                                ><input v-model="cache.temp" class="uk-checkbox" type="checkbox" /><span class="uk-margin-small-left">{{ "Temporary Files" | trans }}</span></label
                            >
                        </p>
                    </div>
                </div>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">
                        {{ "Cancel" | trans }}
                    </button>
                    <button class="uk-button uk-button-primary" @click.prevent="clear">
                        {{ "Clear" | trans }}
                    </button>
                </div>
            </form>
        </v-modal>
    </div>
</template>

<script>
var Cache = {
    mixins: [Theme.Mixins.Helper],

    section: {
        label: "Cache",
        icon: "history",
        priority: 30,
    },

    props: ["config", "options"],

    data() {
        return {
            caches: window.$caches,
            cache: {},
        };
    },

    methods: {
        open() {
            this.$set(this, "cache", { cache: true });
            this.$refs.modal.open();
        },

        clear() {
            this.$http.post("admin/system/cache/clear", { caches: this.cache }).then(function () {
                this.$notify(this.$trans("Cache cleared."));
            });

            this.$refs.modal.close();
        },
    },
};

export default Cache;

window.Settings.components["system-cache"] = Cache;
</script>
