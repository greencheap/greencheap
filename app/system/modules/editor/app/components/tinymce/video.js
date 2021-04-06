/**
 * Editor Video plugin.
 */

export default {
    name: "plugin-video",

    plugin: true,

    created() {
        if (typeof tinyMCE === "undefined") {
            return;
        }

        var vm = this;

        this.$parent.editor.plugins.push("media");
        this.$parent.editor.plugins.push("-greencheapVideo");
        tinyMCE.PluginManager.add("greencheapVideo", function (editor) {
            var showDialog = function () {
                var query,
                    src,
                    attributes = {},
                    element = editor.selection.getNode(),
                    video = {};

                if (element.nodeName === "IMG" && element.hasAttribute("data-mce-object")) {
                    editor.selection.select(element);

                    Object.keys(element.attributes).forEach(function (key) {
                        var name = element.attributes[key].name;

                        if (name === "width" || name === "height" || ((name = name.match(/data-mce-p-(.*)/)) && (name = name[1]))) {
                            video[name] = element.attributes[key].nodeValue === "" || element.attributes[key].nodeValue;
                        }
                    });
                } else if (element.nodeName === "SPAN" && element.hasAttribute("data-mce-object") && (element = element.firstChild)) {
                    src = element.getAttribute("src");
                    src = src.split("?");
                    query = src[1];
                    src = src[0];
                    String(query)
                        .split("&")
                        .forEach(function (param) {
                            param = param.split("=");
                            video[param[0]] = param[1];
                        });

                    video.src = src;
                    video.width = element.getAttribute("width");
                    video.height = element.getAttribute("height");

                    Object.keys(element.attributes).forEach(function (key) {
                        var name = element.attributes[key].name;

                        if (name !== "src" && name !== "width" && name !== "height") {
                            attributes[name] = element.attributes[key].nodeValue;
                        }
                    });
                }

                var PickerComponent = Vue.extend(vm.$parent.$options.utils["video-picker"]);

                var picker = new PickerComponent({
                    parent: vm,
                    data: function () {
                        return { video: { data: video } };
                    },
                })
                    .$mount()
                    .$on("select", function (video) {
                        var content, src, match;

                        delete video.data.playlist;

                        if ((match = picker.isYoutube)) {
                            src = "https://www.youtube.com/embed/" + match[1] + "?";

                            if (video.data.loop) {
                                video.data.playlist = match[1];
                            }
                        } else if ((match = picker.isVimeo)) {
                            src = "https://player.vimeo.com/video/" + match[3] + "?";
                        }

                        if (src) {
                            Object.keys(video.data).forEach(function (attr) {
                                if (attr === "src" || attr === "width" || attr === "height") {
                                    return;
                                }

                                src += attr + "=" + (_.isBoolean(video.data[attr]) ? Number(video.data[attr]) : video.data[attr]) + "&";
                            });

                            attributes.src = src.slice(0, -1);
                            attributes.width = video.data.width || 690;
                            attributes.height = video.data.height || 390;
                            attributes.allowfullscreen = true;

                            content = "<iframe";
                            Object.keys(attributes).forEach(function (attr) {
                                content += " " + attr + (_.isBoolean(attributes[attr]) ? "" : '="' + attributes[attr] + '"');
                            });

                            content += "></iframe>";
                        } else {
                            content = "<video";

                            Object.keys(video.data).forEach(function (attr) {
                                var value = video.data[attr];
                                if (value) {
                                    content += " " + attr + (_.isBoolean(value) ? "" : '="' + value + '"');
                                }
                            });

                            content += "></video>";
                        }

                        editor.selection.setContent("");
                        editor.insertContent(content);

                        editor.fire("change");
                    });
            };

            editor.ui.registry.addIcon("media", '<svg width="24" height="24"><path d="M4 3h16c.6 0 1 .4 1 1v16c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V4c0-.6.4-1 1-1zm1 2v14h14V5H5zm4.8 2.6l5.6 4a.5.5 0 0 1 0 .8l-5.6 4A.5.5 0 0 1 9 16V8a.5.5 0 0 1 .8-.4z" fill-rule="nonzero"></path></svg>');

            editor.ui.registry.addToggleButton("media", {
                tooltip: "Insert/edit video",
                icon: "media",
                onAction: function () {
                    showDialog();
                },
                onSetup: function (api) {
                    return editor.selection.selectorChangedWithUnbind("img[data-mce-object], span[data-mce-object]", function (state) {
                        api.setActive(state);
                        if (state) showDialog();
                    }).unbind;
                },
            });

            editor.ui.registry.addMenuItem("media", {
                icon: "media",
                text: "Insert/edit video",
                context: "insert",
                onAction: function () {
                    showDialog();
                },
            });
        });
    },
};
