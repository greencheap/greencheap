/**
 * Editor Link plugin.
 */

export default {
    name: "plugin-link",

    plugin: true,

    created() {
        if (typeof tinyMCE === "undefined") {
            return;
        }

        var vm = this;

        this.$parent.editor.plugins.push("-greencheapLink");
        tinyMCE.PluginManager.add("greencheapLink", function (editor) {
            var showDialog = function () {
                // return editor.windowManager.open({
                var element = editor.selection.getNode();

                if (element.nodeName === "A") {
                    editor.selection.select(element);
                    var link = { link: element.attributes.href ? element.attributes.href.nodeValue : "", txt: element.innerHTML };
                } else {
                    element = document.createElement("a");
                    link = {};
                }

                var Picker = Vue.extend(vm.$parent.$options.utils["link-picker"]);

                new Picker({
                    parent: vm,
                    data: {
                        link: link,
                    },
                })
                    .$mount()
                    .$on("select", function (link) {
                        element.setAttribute("href", "");

                        var attributes = Object.keys(element.attributes).reduce(function (previous, key) {
                            var name = element.attributes[key].name;

                            if (name === "data-mce-href") {
                                return previous;
                            }

                            return previous + " " + name + '="' + (name === "href" ? link.link : element.attributes[key].nodeValue) + '"';
                        }, "");

                        editor.selection.setContent("<a" + attributes + ">" + link.txt + "</a>");

                        editor.fire("change");
                    });
                // })
            };

            editor.on("click", function (e) {
                if (e.target.nodeName == "A") {
                    showDialog();
                }
            });

            editor.ui.registry.addToggleButton("link", {
                tooltip: "Insert/edit link",
                icon: "link",
                onAction: function () {
                    showDialog();
                },
                onSetup: function (api) {
                    return editor.selection.selectorChangedWithUnbind("a", api.setActive).unbind;
                },
            });

            editor.ui.registry.addMenuItem("link", {
                context: "insert",
                icon: "link",
                text: "Insert/edit link",
                onAction: function () {
                    showDialog();
                },
            });
        });
    },
};
