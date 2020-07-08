/**
 * Editor Image plugin.
 */

export default {

    name: "plugin-image",

    plugin: true,

    created() {

        if (typeof tinyMCE === 'undefined') {
            return;
        }

        var vm = this;

        this.$parent.editor.plugins.push('-greencheapImage');
        tinyMCE.PluginManager.add('greencheapImage', function (editor) {

            var showDialog = function () {

                var element = editor.selection.getNode();

                if (element.nodeName === 'IMG' && !element.hasAttribute('data-mce-object')) {
                    editor.selection.select(element);
                    var image = {src: element.attributes.src.nodeValue, alt: element.attributes.alt.nodeValue};
                } else {
                    element = new Image() || document.createElement('img');
                    image = {};
                }

                var Picker = Vue.extend(vm.$parent.$options.utils['image-picker']);

                new Picker({
                    name: 'image-picker',
                    parent: vm,
                    data: {
                        image: {data: image}
                    }
                }).$mount()
                  .$on('select', function (image) {

                        element.setAttribute('src', '');
                        element.setAttribute('alt', '');

                        var attributes = Object.keys(element.attributes).reduce(function (previous, key) {
                            var name = element.attributes[key].name;

                            if (name === 'data-mce-src') {
                                return previous;
                            }

                            return previous + ' ' + name + '="' + (image.data[name] || element.attributes[key].nodeValue) + '"';
                        }, '');

                        editor.selection.setContent(
                            '<img' + attributes + '>'
                        );

                        editor.fire('change');

                    });
            };

            editor.ui.registry.addToggleButton('image', {
                tooltip: 'Insert/edit image',
                icon: 'image',
                onAction: function () {
                    showDialog();
                },
                onSetup: function(api) {
                    return editor.selection.selectorChangedWithUnbind('img:not([data-mce-object],[data-mce-placeholder]),figure.image', function(state){
                        api.setActive(state);
                        if (state) showDialog();
                    }).unbind;
                }
            });

            editor.ui.registry.addMenuItem('image', {
                icon: 'image',
                text: 'Insert/edit image',
                context: 'insert',
                onAction: function() {
                    showDialog();
                }
            });

        });
    }

};
