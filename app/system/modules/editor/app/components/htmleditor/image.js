/**
 * Editor Image plugin.
 */

import { on, attr, findAll } from 'uikit-util';
import ImagePreview from './image-preview.vue';

export default {

    name: 'image-plugin',

    data: () => ({images: []}),

    plugin: true,

    created() {

        var vm = this, editor = this.$parent.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        // editor
        on(editor.$el, 'action.image', function (e, editor) {
            e.stopImmediatePropagation();
            vm.openModal(_.find(vm.images, function (img) {
                return img.inRange(editor.getCursor());
            }));
        });

        on(editor.$el, 'render', function () {
            var regexp = editor.getMode() != 'gfm' ? /<img(.+?)>/gi : /(?:<img(.+?)>|!(?:\[([^\n\]]*)])(?:\(([^\n\]]*?)\))?)/gi;
            vm.images = editor.replaceInPreview(regexp, vm.replaceInPreview);
        });

        on(editor.$el, 'renderLate', function () {

            while (vm.$children.length) {
                vm.$children[0].$destroy();
            }

            Vue.nextTick(function () {
                findAll('image-preview', editor.preview).forEach(function (el) {
                    var Wrapper = vm.getWrapper(attr(el, 'index')),
                        Component = new Wrapper();
                    Component.$mount(el);
                });
            });
        });
    },

    methods: {
        getWrapper(index) {
            return Vue.extend({
                name      : 'wrapper',
                parent    : this,
                components: this.$options.components,
                data      : () => this.$data,
                render    : (h) => h('image-preview',{props: {index: index}}),
                methods   : { openModal(image) { return this.$parent.openModal(image) } }
            })
        },

        openModal(image) {

            var parser = new DOMParser(), editor = this.$parent.editor, cursor = editor.editor.getCursor();

            if (!image) {
                image = {
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            var imagePicker = new this.$parent.$options.utils['image-picker']({
                parent: this,
                data: {
                    image: image
                }
            }).$mount();

            imagePicker.$on('select', function (image) {

                var content;

                if ((image.tag || editor.getCursorMode()) == 'html') {

                    if (!image.anchor) {
                        image.anchor = parser.parseFromString('<img>', "text/html").body.childNodes[0];;
                    }

                    image.anchor.setAttribute('src', image.data.src);
                    image.anchor.setAttribute('alt', image.data.alt);

                    content = image.anchor.outerHTML;

                } else {
                    content = '![' + image.data.alt + '](' + image.data.src + ')';
                }

                image.replace(content);
            });
        },

        replaceInPreview(data, index) {
            var parser = new DOMParser();

            data.data = {};
            if (data.matches[0][0] == '<') {
                data.anchor = parser.parseFromString(data.matches[0], "text/html").body.childNodes[0];
                data.data.src = data.anchor.attributes.src ? data.anchor.attributes.src.nodeValue : '';
                data.data.alt = data.anchor.attributes.alt ? data.anchor.attributes.alt.nodeValue : '';
                data.tag = 'html';
            } else {
                data.data.src = data.matches[3];
                data.data.alt = data.matches[2];
                data.tag = 'gfm';
            }

            return '<image-preview index="' + index + '"></image-preview>';
        }

    },

    components: {
        'image-preview': ImagePreview
    }

};
