import { $, attr, css, removeAttr } from 'uikit-util';

export default {

    name: "editor-tinymce",

    data() {
        return {
            plugins: [],
            toolbar: ''
        };
    },

    created() {
        var vm            = this,
            baseURL       = $editor.root_url + '/app/assets/tinymce',
            param         = 'editor.tinymce.toolbar',
            model         = _.get(this.$parent, '$vnode.data.model.expression');

        param = !model ? param : param + '.' + model;
        this.toolbar = this.$session.get(param, 0);

        this.$parent.editor = this;

        this.$asset({
            js: [baseURL + '/tinymce.min.js']
        }).then(function () {

            this.$emit('ready');

            tinyMCE.baseURL = baseURL;
            tinyMCE.suffix = '.min';

            this.$parent.editor = tinyMCE.init(_.merge({

                skin_url: $editor.root_url + '/app/assets/tinymce_skin',

                height: this.$parent.height + 12,

                mode: "exact",

                menubar: false,

                branding: false,

                plugins: [
                    vm.plugins,
                    'autolink lists charmap hr anchor media',
                    'visualblocks fullscreen',
                    'paste',
                ],

                toolbar: [
                    'bold italic bullist numlist blockquote hr alignleft aligncenter alignright link image media toggletoolbar fullscreen',
                    'formatselect underline alignjustify strikethrough charmap forecolor backcolor removeformat outdent indent pastetext visualblocks textPicker widgetPicker undo redo'
                ],

                fontsize_formats: '10px 11px 12px 14px 16px 18px 24px 36px 48px',

                toolbar_item_size: "small",

                forced_root_block : "",

                force_br_newlines: true,

                force_p_newlines: "",

                document_base_url: Vue.url.options.root + '/',

                elements: [this.$parent.$refs.editor],

                element_format: 'html',

                entity_encoding: "raw",

                verify_html : false,

                setup(editor) {

                    editor.on('init', function () {
                        vm.$parent.ready = true;
                    });

                    editor.ui.registry.addIcon('more', '<svg aria-hidden="true" role="img" focusable="false" class="dashicon dashicons-ellipsis" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M5 10c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm12-2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-7 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>');
                    editor.ui.registry.addToggleButton('toggletoolbar', {
                        icon: 'more',
                        onAction: function (api) {
                            var toolbar2 = $('.tox-tinymce .tox-toolbar:nth-child(2)', editor.editorContainer);
                            if (!api.isActive()) {
                                removeAttr(toolbar2, 'hidden');
                                vm.$session.set(param, 1);
                            } else {
                                vm.$session.set(param, 0);
                                attr(toolbar2, 'hidden', '');
                            }
                            api.setActive(!api.isActive());
                        },
                        onSetup: function (api) {
                            vm.$nextTick(function() {
                                var toolbar2 = $('.tox-tinymce .tox-toolbar:nth-child(2)', editor.editorContainer),
                                    toolbar1 = $('.tox-tinymce .tox-toolbar > .tox-toolbar__group:first-child', editor.editorContainer);
                                if (!vm.toolbar) {
                                    attr(toolbar2, 'hidden', '');
                                } else {
                                    api.setActive(true);
                                }
                                css(toolbar1, 'border', 'none');
                            })
                        }
                    });
                },

                init_instance_callback(editor) {
                    vm.tiny = editor;

                    var update = function (value) {
                        this.tiny.setContent(value || '', {format: 'text'});
                    };

                    var unbind = vm.$watch('$parent.content', update, {immediate: true});

                    editor.on('change', function () {

                        unbind();

                        vm.$parent.content = editor.getContent();

                        unbind = vm.$watch('$parent.content', update);

                    });

                    editor.on('undo', function () {
                        editor.fire('change');
                    });

                    editor.on('redo', function () {
                        editor.fire('change');
                    });

                    editor.on('keydown', function(event) {
                        if((event.ctrlKey || event.metaKey) && event.which == 83) {
                            if (typeof vm.$root.save === "function") {
                                event.preventDefault();
                                vm.$root.save();
                                return false;
                            }
                        }
                    });

                },

                save_onsavecallback() {

                    if (vm.$parent.$refs.editor.form) {
                        var event = document.createEvent('HTMLEvents');
                        event.initEvent('submit', true, false);
                        vm.$parent.$refs.editor.form.dispatchEvent(event);
                    }

                }

            }, $editor));

        });

    }

};
