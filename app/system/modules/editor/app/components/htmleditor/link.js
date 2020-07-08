/**
 * Editor Link plugin.
 */

import { on, attr, findAll } from 'uikit-util';
import LinkPreview from './link-preview.vue';

export default {

    name: 'link-plugin',

    data: () => ({links: []}),

    plugin: true,

    created() {

        var vm = this, editor = this.$parent.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        on(editor.$el, 'action.link', function (e, editor) {
            e.stopImmediatePropagation();
            vm.openModal(_.find(vm.links, function (link) {
                return link.inRange(editor.getCursor());
            }));
        });

        on(editor.$el, 'render', function () {
            var regexp = editor.getMode() != 'gfm' ? /<a(?:\s.+?>|\s*>)(?:[^<]*)<\/a>/gi : /<a(?:\s.+?>|\s*>)(?:[^<]*)<\/a>|(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?/gi;
            vm.links = editor.replaceInPreview(regexp, vm.replaceInPreview);
        });

        on(editor.$el, 'renderLate', function () {
            while (vm.$children.length) {
                vm.$children[0].$destroy();
            }

            Vue.nextTick(function () {
                findAll('link-preview', editor.preview).forEach(function (el) {
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
                render    : (h) => h('link-preview',{props: {index: index}}),
                methods   : { openModal(link) { return this.$parent.openModal(link) } }
            })
        },

        openModal(link) {

            var parser = new DOMParser(), editor = this.$parent.editor, cursor = editor.editor.getCursor();

            if (!link) {
                link = {
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            var linkPicker = new this.$parent.$options.utils['link-picker']({
                parent: this,
                data: {
                    link: link
                }
            }).$mount();

            linkPicker.$on('select', function (link) {

                if (!link.anchor) {
                    link.anchor = parser.parseFromString('<a></a>', "text/html").body.childNodes[0];
                }

                link.anchor.setAttribute('href', link.link);
                link.anchor.innerHTML = link.txt;

                link.replace(link.anchor.outerHTML);
            });
        },

        replaceInPreview(data, index) {
            var parser = new DOMParser();

            data.data = {};
            if (data.matches[0][0] == '<') {
                data.anchor = parser.parseFromString(data.matches[0], "text/html").body.childNodes[0];
                data.link = data.anchor.attributes.href ? data.anchor.attributes.href.nodeValue : '';
                data.txt = data.anchor.innerHTML;
            } else {

                if (data.matches[data.matches.length - 1][data.matches[data.matches.length - 2] - 1] == '!') return false;

                data.link = data.matches[2];
                data.txt = data.matches[1];

            }

            return '<link-preview index="' + index + '"></link-preview>';
        }

    },

    components: {
        'link-preview': LinkPreview
    }

};
