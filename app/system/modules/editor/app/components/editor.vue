<template>
    <div :class="['pk-editor', editorMode, { 'uk-invisible': !ready && !editorMode }]">
        <template v-if="editorMode === 'split'">
            <ul ref="tab" class="uk-subnav uk-flex-right" uk-switcher>
                <li>
                    <a href="">{{ "Visual" | trans }}</a>
                </li>
                <li>
                    <a href="">{{ "Code" | trans }}</a>
                </li>
            </ul>
            <ul class="uk-switcher" :class="{ 'uk-invisible': !ready }">
                <li ref="visual">
                    <textarea autocomplete="off" :style="{ height: height + 'px' }" :class="{ 'uk-invisible': !show }" ref="editor" v-model="content"></textarea>
                </li>
                <li ref="code">
                    <textarea autocomplete="off" :style="{ height: height + 'px' }" :class="{ 'uk-invisible': !show }" ref="editor-code" v-model="content"></textarea>
                </li>
            </ul>
        </template>
        <template v-else>
            <textarea autocomplete="off" :style="{ height: height + 'px' }" :class="{ 'uk-invisible': !show }" ref="editor" v-model="content"></textarea>
        </template>
    </div>
</template>

<script>
import { $, on, addClass, closest } from "uikit-util";

// Utils
import ImagePicker from "./image-picker.vue";
import VideoPicker from "./video-picker.vue";
import LinkPicker from "./link-picker.vue";

// Codemirror
import EditorCode from "./editor-code";

// HTMLEditor
import EditorHtml from "./htmleditor/editor-html";
import EditorHtmlPluginLink from "./htmleditor/link";
import EditorHtmlPluginImage from "./htmleditor/image";
import EditorHtmlPluginVideo from "./htmleditor/video";
import EditorHtmlPluginUrl from "./htmleditor/url";

// TinyMCE
import TinyMCE from "./tinymce/editor-tinymce";
import TinyMCEPluginLink from "./tinymce/link";
import TinyMCEPluginImage from "./tinymce/image";
import TinyMCEPluginVideo from "./tinymce/video";

export default {
    props: ["type", "mode", "value", "options"],

    data() {
        return {
            editor: {},
            height: 500,
            show: false,
            active: 0,
            ready: false,
            // TODO
            content: this.value,
        };
    },

    // TODO
    editors: {
        html: {
            "editor-html": EditorHtml,
            "plugin-link": EditorHtmlPluginLink,
            "plugin-image": EditorHtmlPluginImage,
            "plugin-video": EditorHtmlPluginVideo,
            "plugin-url": EditorHtmlPluginUrl,
        },
        tinymce: {
            "editor-html": TinyMCE,
            "plugin-link": TinyMCEPluginLink,
            "plugin-image": TinyMCEPluginImage,
            "plugin-video": TinyMCEPluginVideo,
        },
        code: {
            "editor-html": EditorCode,
        },
    },

    unsplit: ["html", "code", "textarea"],

    computed: {
        editorType() {
            return this.type || window.$greencheap.editor.editor || "textarea";
        },

        editorMode() {
            var editors = this.$options.unsplit;
            return editors.indexOf(this.editorType) !== -1 ? "" : this.mode || window.$greencheap.editor.mode || "";
        },
    },

    created() {
        this.createEditor();
        this.$on("hook:mounted", this.init);
    },

    mounted() {
        var vm = this;

        if (this.editorMode == "split") {
            this.tab = UIkit.switcher(this.$refs.tab);

            on(this.tab.connects, "show", function (e, tab) {
                if (tab != vm.tab) return false;
                for (var index in tab.toggles) {
                    if (closest($(tab.toggles[index]), "li").classList.contains("uk-active")) {
                        vm.active = index;
                        break;
                    }
                }
            });

            this.tab.show(this.active);
        }
    },

    methods: {
        createEditor() {
            var editors = Object.keys(this.$options.editors);

            if (editors.indexOf(this.editorType) !== -1) {
                _.extend(this.$options.components, this.$options.editors[this.editorType]);
            }
        },

        init() {
            if (this.editorMode === "split") {
                var el = this.$el.previousElementSibling || this.$el.parentNode.previousElementSibling;
                el && addClass(el, "uk-position-absolute");
            }

            if (this.options && this.options.height) {
                this.height = this.options.height;
            }

            if (this.$el.hasAttributes()) {
                var attrs = this.$el.attributes;

                for (var i = attrs.length - 1; i >= 0; i--) {
                    if (attrs[i].name != "class") {
                        this.$refs.editor.setAttribute(attrs[i].name, attrs[i].value);
                        this.$el.removeAttribute(attrs[i].name);
                    }
                }
            }

            var components = this.$options.components,
                type = "editor-" + this.type,
                self = this,
                EditorComponent = components[type] || components["editor-html"] || components["editor-textarea"];

            var Editor = Vue.extend(EditorComponent);

            new Editor({ parent: this }).$on("ready", function () {
                _.forIn(
                    self.$options.components,
                    function (Component) {
                        if (Component.plugin) {
                            var Plugin = Vue.extend(Component);
                            new Plugin({ parent: self });
                        }
                    },
                    this
                );

                if (self.editorMode == "split") {
                    self.addCode();
                }
            });
        },

        addCode() {
            var vm = this,
                CodeEditor = Vue.extend(this.$options.components["editor-code"]);

            new CodeEditor({ parent: this });
        },
    },

    components: {
        "editor-textarea": {
            created: function () {
                this.$emit("ready");
                this.$set(this.$parent, "show", true);
            },
        },

        "editor-code": EditorCode,
    },

    utils: {
        "image-picker": Vue.extend(ImagePicker),
        "video-picker": Vue.extend(VideoPicker),
        "link-picker": Vue.extend(LinkPicker),
    },

    watch: {
        value(content) {
            this.$set(this, "content", content);
        },

        content(content) {
            this.$emit("input", content);
            this.$emit("update:editor", content);
        },
    },
};

Vue.component("v-editor", function (resolve) {
    resolve(require("./editor.vue"));
});
</script>
