<template>
    <div>
        <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
            <div>
                <h2 class="uk-margin-remove">
                    {{ "Misc" | trans }}
                </h2>
            </div>
            <div class="uk-margin-small">
                <button class="uk-button uk-button-primary" type="submit">
                    {{ "Save" | trans }}
                </button>
            </div>
        </div>

        <h3 class="uk-h4 uk-margin-small">{{ "Editor Settings" | trans }}</h3>

        <div class="uk-margin-small">
            <label for="form-user-editor" class="uk-form-label">{{ "Default editor" | trans }}</label>
            <div class="uk-form-controls">
                <div class="uk-margin-small">
                    <select class="uk-select uk-form-width-large" v-model="type">
                        <option :value="e.value" v-for="(e, id) in $options.editors" :key="id">{{ e.name }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="uk-margin-small">
            <label for="form-user-editor" class="uk-form-label">{{ "Display mode" | trans }}</label>
            <div class="uk-form-controls uk-form-controls-text">
                <div class="uk-margin-small">
                    <label>
                        <input type="radio" class="uk-radio" value="" v-model="mode" />
                        <span>{{ "Default" | trans }}</span>
                    </label>
                </div>
                <template v-if="editor.split">
                    <div class="uk-margin-small">
                        <label>
                            <input type="radio" class="uk-radio" value="split" v-model="mode" />
                            <span :class="{ 'uk-text-muted': type === 'codemirror' }">{{ "Spliting visual and code editor" | trans }}</span>
                        </label>
                    </div>
                    <div class="uk-inline uk-form-width-large uk-text-meta">
                        <span>{{ "By default, only one editor is displayed; in split mode, visual and code editors are displayed at the same time." | trans }}</span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    section: {
        label: "Misc",
        icon: "cog",
        priority: 100,
    },

    props: ["config", "options"],

    data() {
        return _.extend(
            {
                type: window.$greencheap.editor.editor || "",
                mode: window.$greencheap.editor.mode || "",
            },
            window.$system
        );
    },

    // TODO
    editors: {
        html: {
            name: "HTML",
            value: "html",
        },
        tinymce: {
            name: "TinyMCE",
            value: "tinymce",
            split: true,
        },
        codemirror: {
            name: "Codemirror",
            value: "code",
        },
    },

    computed: {
        editor() {
            return _.find(this.$options.editors, { value: this.type });
        },
    },

    watch: {
        type(value) {
            if (!this.editor.split) {
                this.mode = "";
            }
        },
    },

    events: {
        "save:settings": function () {
            var option = { "system/editor": { editor: this.type, mode: this.mode } };
            _.extend(this.$parent.options, option);
        },
    },
};
</script>
