/**
 * URL resolver plugin
 */

import { on } from 'uikit-util';

export default {

    name: 'url-plugin',

    plugin: true,

    created() {

        var editor = this.$parent.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        on(editor.$el, 'renderLate', function () {

            editor.replaceInPreview(/src=["'](.+?)["']/gi, function (data) {

                var replacement = data.matches[0];

                if (!data.matches[1].match(/^(\/|http:|https:|ftp:)/i)) {
                    replacement = replacement.replace(data.matches[1], Vue.url(data.matches[1], true));
                }

                return replacement;
            });

        });

    }

};
