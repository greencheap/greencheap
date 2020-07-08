import { on, trigger } from 'uikit-util';

export default {

    name: 'editor-html',

    created() {

        var vm = this,
            baseURL = $editor.root_url;

        this.$parent.$set(this.$parent, 'height', this.$parent.height + 31);

        this.$asset({

            css: [
                baseURL + '/app/assets/codemirror/show-hint.css',
                baseURL + '/app/assets/codemirror/codemirror.css'
            ],
            js: [
                baseURL + '/app/assets/codemirror/codemirror.min.js',
                baseURL + '/app/assets/marked/marked.min.js',
            ]

        }).then(function () {

            var editor = this.$parent.editor = UIkit.htmleditor(this.$parent.$refs.editor, _.extend({
                lblPreview    : this.$trans('Preview'),
                lblCodeview   : this.$trans('Code'),
                lblMarkedview : this.$trans('Markdown'),
                marked        : window.marked,
                CodeMirror    : window.CodeMirror
            }, this.$parent.options));

            on(editor.$el, 'htmleditor-save', function (e, editor) {
                if (editor.element[0].form) {
                    var event = document.createEvent('HTMLEvents');
                    event.initEvent('submit', true, true);
                    editor.element[0].form.dispatchEvent(event);
                }
            });

            on(editor.$el, 'init', function () {
                vm.$parent.ready = true;
            });

            on(editor.$el, 'render', function () {
                var regexp = /<script(.*)>[^<]+<\/script>|<style(.*)>[^<]+<\/style>/gi;
                editor.replaceInPreview(regexp, '');
            });

            this.$watch('$parent.value', function (value) {
                if (value != editor.editor.getValue()) {
                    editor.editor.setValue(value);
                }
            });

            this.$watch('$parent.options.markdown', function (markdown) {
                    trigger(editor.$el, markdown ? 'enableMarkdown' : 'disableMarkdown')
                }, {immediate: true}
            );

            this.$emit('ready');
        })

    }

};
