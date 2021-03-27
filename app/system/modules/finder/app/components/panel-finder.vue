<template>
    <div v-show="items" class="storage">
        <template v-if="!modal">
            <div class="uk-flex uk-flex-between uk-flex-wrap">
                <div class="uk-flex uk-flex-middle uk-flex-wrap">
                    <h2 class="uk-h3 uk-margin-remove">
                        <span v-if="!selected.length">{{ "{0} %count% Files|{1} %count% File|]1,Inf[ %count% Files" | transChoice(count, { count: count }) }}</span>
                        <span v-else>{{
                            "{1} %count% File selected|]1,Inf[ %count% Files selected"
                                | transChoice(selected.length, {
                                    count: selected.length,
                                })
                        }}</span>
                    </h2>
                    <div class="uk-margin-left" v-if="isWritable && selected.length">
                        <ul class="uk-iconnav">
                            <li :class="selected.length !== 1 ? 'uk-disabled' : ''">
                                <a uk-icon="file-edit" :title="'Rename' | trans" uk-tooltip="delay: 500" @click.prevent="rename" />
                            </li>
                            <li :class="!selected.length ? 'uk-disabled' : ''">
                                <a v-confirm="'Delete files?'" uk-icon="trash" :title="'Delete' | trans" uk-tooltip="delay: 500" @click.prevent="remove" />
                            </li>
                        </ul>
                    </div>

                    <div class="uk-search uk-search-default pk-search">
                        <span uk-search-icon />
                        <input v-model="search" class="uk-search-input" type="search" />
                    </div>
                </div>
                <div class="uk-flex uk-flex-middle uk-flex-wrap">
                    <div class="uk-margin-right">
                        <ul class="uk-iconnav">
                            <li :class="{ 'uk-active': view == 'template-table' }">
                                <a uk-icon="table" :title="'Table View' | trans" uk-tooltip="delay: 500" @click.prevent="view = 'template-table'" />
                            </li>
                            <li :class="{ 'uk-active': view == 'template-thumbnail' }">
                                <a uk-icon="thumbnails" :title="'Thumbnails View' | trans" uk-tooltip="delay: 500" @click.prevent="view = 'template-thumbnail'" />
                            </li>
                        </ul>
                    </div>

                    <div class="uk-iconnav">
                        <li>
                            <a uk-icon="folder" :title="'Add Folder' | trans" uk-tooltip="delay: 500" @click.prevent="createFolder()" />
                        </li>
                        <li>
                            <a class="files-upload uk-icon uk-flex uk-flex-middle" :title="'Upload' | trans" uk-tooltip="delay: 500">
                                <div uk-form-custom>
                                    <input type="file" name="files[]" multiple="multiple" />
                                    <span uk-icon="upload" />
                                </div>
                            </a>
                        </li>
                    </div>
                </div>
            </div>

            <div class="uk-flex uk-flex-middle">
                <i uk-icon="icon: database; ratio: 1.25" class="uk-margin-small-right" />
                <ul class="uk-breadcrumb uk-margin-small">
                    <li v-for="(bc, key) in breadcrumbs" :key="key" :class="{ 'uk-active': bc.current }">
                        <span v-if="bc.current">{{ bc.title }}</span>
                        <a v-else @click.prevent="setPath(bc.path)">{{ bc.title }}</a>
                    </li>
                </ul>
            </div>

            <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden />

            <div class="uk-overflow-auto tm-overflow-container" uk-height-viewport="offset-top: true; offset-bottom: 120px">
                <component :is="view" v-show="count" />
                <h3 v-show="!count" class="uk-h2 uk-text-muted uk-text-center">
                    {{ "No files found." | trans }}
                </h3>
            </div>
        </template>

        <template v-else>
            <div class="uk-modal-header">
                <div class="uk-flex uk-flex-between uk-flex-wrap">
                    <div class="uk-flex uk-flex-middle uk-flex-wrap">
                        <div v-if="isWritable && selected.length">
                            <ul class="uk-iconnav">
                                <li :class="selected.length !== 1 ? 'uk-disabled' : ''">
                                    <a uk-icon="file-edit" :title="'Rename' | trans" uk-tooltip="delay: 500" @click.prevent="rename" />
                                </li>
                                <li :class="!selected.length ? 'uk-disabled' : ''">
                                    <a v-confirm="'Delete files?'" uk-icon="trash" :title="'Delete' | trans" uk-tooltip="delay: 500" @click.prevent="remove" />
                                </li>
                            </ul>
                        </div>
                        <div class="uk-search uk-search-default pk-search">
                            <span uk-search-icon />
                            <input v-model="search" class="uk-search-input" type="search" />
                        </div>
                    </div>
                    <div class="uk-flex uk-flex-middle uk-flex-wrap uk-margin-remove">
                        <div class="uk-margin-right">
                            <ul class="uk-iconnav">
                                <li :class="{ 'uk-active': view == 'template-table' }">
                                    <a uk-icon="table" :title="'Table View' | trans" uk-tooltip="delay: 500" @click.prevent="view = 'template-table'" />
                                </li>
                                <li :class="{ 'uk-active': view == 'template-thumbnail' }">
                                    <a uk-icon="thumbnails" :title="'Thumbnails View' | trans" uk-tooltip="delay: 500" @click.prevent="view = 'template-thumbnail'" />
                                </li>
                            </ul>
                        </div>

                        <div class="uk-iconnav">
                            <li>
                                <a uk-icon="folder" :title="'Add Folder' | trans" uk-tooltip="delay: 500" @click.prevent="createFolder()" />
                            </li>
                            <li>
                                <a class="files-upload uk-icon uk-flex uk-flex-middle" :title="'Upload' | trans" uk-tooltip="delay: 500">
                                    <div uk-form-custom>
                                        <input type="file" name="files[]" multiple="multiple" />
                                        <span uk-icon="upload" />
                                    </div>
                                </a>
                            </li>
                        </div>
                    </div>
                </div>

                <div class="uk-flex uk-flex-middle">
                    <i uk-icon="icon: database" class="uk-margin-small-right" />
                    <ul class="uk-breadcrumb">
                        <li v-for="(bc, key) in breadcrumbs" :key="key" :class="{ 'uk-active': bc.current }">
                            <span v-if="bc.current">{{ bc.title }}</span>
                            <a v-else @click.prevent="setPath(bc.path)">{{ bc.title }}</a>
                        </li>
                    </ul>
                </div>
                <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden />
            </div>

            <div class="uk-modal-body">
                <div class="tm-finder-modal-container">
                    <div class="uk-overflow-auto" uk-overflow-auto :class="{ 'uk-flex uk-flex-center uk-flex-middle': !count }">
                        <div class="tm-overflow-container">
                            <component :is="view" v-show="count" />
                            <h3 v-show="!count" class="uk-h2 uk-text-muted uk-text-center">
                                {{ "No files found." | trans }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    name: "panel-finder",

    props: {
        root: { type: String, default: "/" },
        mode: { type: String, default: "write" },
        modal: Boolean,
    },

    multiFinder: {
        label: "Local Storage",
        priority: 0,
    },

    data() {
        return {
            upload: {},
            selected: [],
            items: false,
            search: "",
            path: "",
            view: "",
        };
    },

    created() {
        if (!this.path) {
            this.path = this.$session.get(`finder.${this.root}.path`, "/");
        }

        if (!this.view) {
            this.view = this.$session.get(`finder.${this.root}.view`, "template-table");
        }

        this.$watch("path", function (path) {
            this.load();
            this.$session.set(`finder.${this.root}.path`, path);
        });

        this.$on("hook:mounted", this.initUpload);
    },

    mounted() {
        this.modaloptions = {
            labels: {
                ok: this.$trans("Ok"),
                cancel: this.$trans("Cancel"),
            },
            stack: true,
        };

        this.resource = this.$resource("system/finder{/cmd}");

        this.load().then(function () {
            this.$emit("ready:finder", this);
        });
    },

    watch: {
        view(view) {
            this.$session.set(`finder.${this.root}.view`, view);
        },

        selected() {
            this.$emit("select:finder", this.getSelected(), this);
        },

        search() {
            const vm = this;
            this.$set(
                this,
                "selected",
                _.filter(this.selected, (name) => _.find(vm.searched, { name }))
            );
        },
    },

    computed: {
        breadcrumbs() {
            let path = "";
            const crumbs = [{ path: "/", title: this.$trans("Storage") }] // 'Home'
                .concat(
                    this.path
                        .substr(1)
                        .split("/")
                        .filter((str) => str.length)
                        .map((part) => ({
                            path: (path += `/${part}`),
                            title: part,
                        }))
                );

            crumbs[crumbs.length - 1].current = true;

            return crumbs;
        },

        searched() {
            const vm = this;
            return _.filter(this.items, (file) => !vm.search || file.name.toLowerCase().indexOf(vm.search.toLowerCase()) !== -1);
        },

        count() {
            return this.searched.length;
        },
    },

    methods: {
        /**
         * API
         */

        setPath(path) {
            this.$set(this, "path", path);
        },

        getPath() {
            return this.path;
        },

        getFullPath() {
            return `${(this.getRoot() + this.getPath()).replace(/^\/+|\/+$/g, "")}/`;
        },

        getRoot() {
            return this.root.replace(/^\/+|\/+$/g, "");
        },

        getSelected() {
            return this.selected.map(function (name) {
                return _.find(this.items, { name }).url;
            }, this);
        },

        removeSelection() {
            this.selected = [];
        },

        toggleSelect(name) {
            const index = this.selected.indexOf(name);
            index === -1 ? this.selected.push(name) : this.selected.splice(index, 1);
        },

        isSelected(name) {
            return this.selected.indexOf(name.toString()) != -1;
        },

        createFolder() {
            UIkit.modal.prompt(this.$trans("Folder Name"), "", this.modaloptions).then((name) => {
                if (!name) return;
                this.command("createfolder", { name });
            });
        },

        rename(oldname) {
            if (oldname.target) {
                oldname = this.selected[0];
            }

            if (!oldname) return;

            UIkit.modal.prompt(this.$trans("Name"), oldname).then((newname) => {
                if (!newname) return;

                this.command("rename", { oldname, newname });
            }, _.extend({ title: this.$trans("Rename") }, this.modaloptions));
        },

        remove(names) {
            if (names.target) {
                names = this.selected;
            }

            if (names) {
                this.command("removefiles", { names });
            }
        },

        /**
         * Helper functions
         */

        encodeURI(url) {
            return encodeURI(url).replace(/'/g, "%27");
        },

        isWritable() {
            return this.mode === "w" || this.mode === "write";
        },

        isImage(url) {
            return url.match(/\.(?:gif|jpe?g|png|svg|ico|webp)$/i);
        },

        isFileExt(name, ext) {
            const regex = `(?:${ext})$`;
            return name.match(new RegExp(regex, "i"));
        },

        isVideo(url) {
            return url.match(/\.(mpeg|ogv|mp4|webm|wmv)$/i);
        },

        command(cmd, params) {
            return this.resource.save({ cmd }, _.extend({ path: this.path, root: this.getRoot() }, params)).then(
                function (res) {
                    this.load();
                    this.$notify(res.data.message, res.data.error ? "danger" : "");
                },
                function (res) {
                    this.$notify(res.status == 500 ? "Unknown error." : res.data, "danger");
                }
            );
        },

        load() {
            return this.resource.get({ path: this.path, root: this.getRoot() }).then(
                function (res) {
                    this.$set(this, "items", res.data.items || []);
                    this.$set(this, "selected", []);
                    this.$emit("path:finder", this.getFullPath(), this);
                },
                function () {
                    this.$notify("Unable to access directory.", "danger");
                }
            );
        },

        initUpload() {
            const bar = document.getElementById("js-progressbar");

            const finder = this;
            const settings = {
                url: this.$url.route("system/finder/upload"),
                multiple: true,

                beforeAll(data) {
                    _.extend(data.params, {
                        path: finder.path,
                        root: finder.getRoot(),
                        _csrf: $greencheap.csrf,
                    });
                },

                loadStart(e) {
                    bar.removeAttribute("hidden");
                    bar.max = e.total;
                    bar.value = e.loaded;
                },

                progress(e) {
                    bar.max = e.total;
                    bar.value = e.loaded;
                },

                loadEnd(e) {
                    bar.max = e.total;
                    bar.value = e.loaded;
                },

                completeAll(response) {
                    var data = response;

                    try {
                        var data = JSON.parse(data.responseText);
                    } catch (e) {
                        try {
                            var data = JSON.parse(data.responseText.substring(data.responseText.lastIndexOf("{"), data.responseText.lastIndexOf("}") + 1));
                            var { message } = data;
                        } catch (e) {
                            var message = "Unable load file(s).";
                        }

                        finder.$notify(message, "danger");
                        return;
                    }

                    finder.load();

                    finder.$notify(data.message, data.error ? "danger" : "");

                    setTimeout(() => {
                        bar.setAttribute("hidden", "hidden");
                    }, 1000);
                },
            };

            UIkit.upload(this.$el.querySelector(".files-upload"), settings);
            UIkit.upload(UIkit.util.parents(this.$el, ".uk-modal").length ? this.$el : UIkit.util.$("html"), settings);
        },
    },

    components: {
        "template-table": {
            template: require("../templates/table.html"),
        },
        "template-thumbnail": {
            template: require("../templates/thumbnail.html"),
        },
    },
};

Vue.component("panel-finder", (resolve) => {
    resolve(require("./panel-finder.vue"));
});
</script>
