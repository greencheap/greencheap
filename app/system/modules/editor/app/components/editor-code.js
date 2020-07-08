import { css, closest, removeAttr, height, trigger } from 'uikit-util';

export default {

    name: 'editor-code',

    created: function () {

        var baseURL = $editor.root_url + '/app/assets/codemirror';

        this.$asset({
            css: [
                baseURL + '/show-hint.css',
                baseURL + '/codemirror.css'
            ],
            js: [
                baseURL + '/codemirror.min.js'
            ]
        }).then(this.init);
    },

    methods: {
        init() {

            var self = this,
                mode = this.$parent.editorMode,
                $el  = (mode != 'split') ? this.$parent.$refs['editor'] : this.$parent.$refs['editor-code'];

            this.editor = CodeMirror.fromTextArea($el, _.extend({
                mode: 'htmlmixed',
                dragDrop: false,
                autoCloseTags: true,
                matchTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                indentUnit: 4,
                indentWithTabs: false,
                tabSize: 4,
                lineNumbers: true,
                lineWrapping: true,
                extraKeys: {
                    "F11": function(cm) {
                        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    },
                    "Esc": function(cm) {
                        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    }
                }
            }, this.$parent.options));

            this.editor.setSize(null, this.$parent.height - 2);

            this.editor.refresh();

            if (mode != 'split') {
                this.$parent.ready = true;
            }

            this.editor.on('change', function () {
                self.editor.save();
                trigger($el, 'input');
            });

            this.$watch('$parent.active', function (state) {
                if (mode == 'split' && state == 1) {
                    this.editor.setSize(null, this.getHeight(this.$parent.$refs.visual) - 2);
                    this.editor.refresh();
                }
            });

            this.$watch('$parent.content', function (value) {
                if (value != this.editor.getValue()) {
                    this.editor.setValue(value);
                    this.editor.refresh();
                }
            });

            this.observe($el, mode);

            this.$emit('ready');
        },

        getHeight(el) {
            var h = 0;

            if(css(el, 'display') !== 'none') {
                return height(el);
            }
            css(el, {'position': 'absolute', 'visibility': 'hidden','display': 'block'});
            h = height(el);
            removeAttr(el,'style');

            return h;
        },

        observe(el, mode) {

            var vm = this;
            var observer, element = closest(el, 'li');

            if (mode === 'split') return;
            if (!element) return;

            observer = new MutationObserver(function(){
               if(element.style.display !='none' ){
                    vm.editor.refresh();
               }
            });

            observer.observe(element,  { attributes: true, childList: true });
        }
    }

};
