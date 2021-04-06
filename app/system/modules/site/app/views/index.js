import { $, on, css, addClass, removeClass, hasClass, toNodes } from "uikit-util";
import { VueNestable, VueNestableHandle } from "vue-nestable";
import { ValidationObserver, VInput } from "SystemApp/components/validation.vue";

import isMobile from "ismobilejs";

Vue.ready({
    name: "site",

    el: "#site",

    mixins: [Theme.Mixins.Helper],

    data() {
        const vm = this;
        return _.merge(
            {
                edit: {}, // undefined,
                menu: this.$session.get("site.menu", {}),
                menus: [],
                nodes: [],
                treedata: [],
                selected: [],
            },
            window.$data
        );
    },

    created() {
        this.Menus = this.$resource("api/site/menu{/id}");
        this.Nodes = this.$resource("api/site/node{/id}");

        const vm = this;
        this.load().then(() => {
            vm.$set(vm, "menu", _.find(vm.menus, { id: vm.menu.id }) || vm.menus[0]);
        });

        this.$watch(
            (vm) => (vm.menu, vm.nodes, Date.now()),
            () => {
                this.tree("update");
            },
            { deep: true }
        );

        on(window, "resize", () => {
            this.propWidth();
        });
    },

    mounted() {},

    methods: {
        propWidth() {
            css(this.$refs["table-header"], {
                minWidth: "100%",
                width: this.$refs.nestable.$el.offsetWidth ? this.$refs.nestable.$el.offsetWidth : "100%",
                opacity: "1",
            });
        },

        change(value, options) {
            if (!options || !options.pathTo) return;

            const vm = this;

            var updateTree = (tree, parent_id) => {
                _.forEach(tree, (item, id) => {
                    item.priority = id;
                    item.parent_id = parent_id;
                    if (item.children.length) updateTree(item.children, item.id);
                });
            };

            updateTree(this.treedata, 0);

            vm.Nodes.save(
                { id: "updateOrder" },
                {
                    menu: vm.menu.id,
                    nodes: vm.tree("flatten"), // vm.nestableList(this.treedata)
                }
            ).then(vm.load, () => {
                vm.$notify("Reorder failed.", "danger");
            });
        },

        tree(...args) {
            const [fn, ...props] = arguments;
            const vm = this;
            const methods = {
                unflatten() {
                    let [array, parent, tree] = arguments;
                    const self = this;

                    tree = typeof tree !== "undefined" ? tree : [];
                    parent = typeof parent !== "undefined" ? parent : { id: 0 };

                    const children = _.filter(array, (child) => child.parent_id == parent.id);

                    if (!_.isEmpty(children)) {
                        if (parent.id == 0) {
                            tree = children;
                        } else {
                            parent.children = children;
                        }
                        _.each(children, (child) => {
                            self.unflatten(array, child);
                        });
                    }

                    return tree;
                },
                flatten() {
                    const treeStructure = { children: vm.treedata };

                    const flatten = (children, extractChildren, level, order) =>
                        Array.prototype.concat.apply(
                            children.map((x) => ({ ...x, level: level || 1, order: x.priority || 0 })),
                            children.map((x) => flatten(extractChildren(x) || [], extractChildren, (level || 1) + 1))
                        );

                    const extractChildren = (x) => x.children;

                    const flat = flatten(extractChildren(treeStructure), extractChildren).map((x) => delete x.children && x);

                    return flat;
                },
                update() {
                    let nodes = vm.nodes.map((entry) => {
                        entry.class = "check-item";
                        return entry;
                    });
                    nodes = _(nodes).filter({ menu: vm.menu.id }).sortBy("priority").value();
                    vm.treedata = this.unflatten(nodes);
                    vm.$nextTick(() => {
                        vm.propWidth();
                    });
                },
            };

            return methods[fn] && typeof methods[fn] === "function" ? methods[fn](props) : false;
        },

        load() {
            const vm = this;
            return Vue.Promise.all([this.Menus.query(), this.Nodes.query()]).then(
                (responses) => {
                    vm.$set(vm, "menus", responses[0].data);
                    vm.$set(vm, "nodes", responses[1].data);
                    vm.$set(vm, "selected", []);

                    if (!_.find(vm.menus, { id: vm.menu.id })) {
                        vm.$set(vm, "menu", vm.menus[0]);
                    }
                },
                () => {
                    vm.$notify("Loading failed.", "danger");
                }
            );
        },

        isActive(menu) {
            return this.menu && this.menu.id === menu.id;
        },

        selectMenu(menu) {
            this.$set(this, "selected", []);
            this.$set(this, "menu", menu);
            this.$session.set("site.menu", menu);
        },

        removeMenu(e, menu) {
            this.Menus.delete({ id: menu.id }).finally(this.load);
        },

        editMenu(e, menu) {
            if (!menu) {
                menu = {
                    id: "",
                    label: "",
                };
            }

            this.$set(this, "edit", _.merge({ positions: [] }, menu));
            this.$refs.modal.open();
        },

        saveMenu(menu) {
            this.Menus.save({ menu }).then(this.load, function (res) {
                this.$notify(res.data, "danger");
            });

            this.cancel();
        },

        getMenu(position) {
            return _.find(this.menus, (menu) => _.includes(menu.positions, position));
        },

        cancel() {
            this.$refs.modal.close();
        },

        status(status) {
            const nodes = this.getSelected();

            nodes.forEach((node) => {
                node.status = status;
            });

            this.Nodes.save({ id: "bulk" }, { nodes }).then(function () {
                this.load();
                this.$notify("Page(s) saved.");
            });
        },

        moveNodes(menu) {
            const vm = this;
            const nodes = this.getSelected();

            var updateChilds = function (node) {
                _.forEach(node.children, (item) => {
                    const search = _.filter(nodes, (e) => e.id == item.id);
                    if (!search.length) {
                        const key = Object.keys(vm.nodes).find((key) => vm.nodes[key].id === item.id);
                        vm.nodes[key].parent_id = null;
                        nodes.push(vm.nodes[key]);
                    }
                    if (item.children) updateChilds(item);
                });
            };

            nodes.forEach((node) => {
                node.parent_id = null;
                node.menu = menu;
                updateChilds(node);
            });

            this.Nodes.save({ id: "bulk" }, { nodes }).then(function () {
                this.load();
                this.$notify(
                    this.$trans("Pages moved to %menu%.", {
                        menu: _.find(
                            this.menus.concat({
                                id: "trash",
                                label: this.$trans("Trash"),
                            }),
                            { id: menu }
                        ).label,
                    })
                );
            });
        },

        removeNodes() {
            if (this.menu.id !== "trash") {
                const nodes = this.getSelected();

                nodes.forEach((node) => {
                    node.status = 0;
                });

                this.moveNodes("trash");
            } else {
                this.Nodes.delete({ id: "bulk" }, { ids: this.selected }).then(function () {
                    this.load();
                    this.$notify("Page(s) deleted.");
                });
            }
        },

        getType(node) {
            return _.find(this.types, { id: node.type });
        },

        getSelected() {
            return this.nodes.filter(function (node) {
                return this.isSelected(node);
            }, this);
        },

        isSelected(node, children) {
            const vm = this;
            if (_.isArray(node)) {
                return _.every(node, (node) => vm.isSelected(node, children), this);
            }

            return this.selected.indexOf(node.id) !== -1 && (!children || !this.tree[node.id] || this.isSelected(this.tree[node.id], true));
        },

        toggleSelect(node) {
            const index = this.selected.indexOf(node.id);

            if (index == -1) {
                this.selected.push(node.id);
            } else {
                this.selected.splice(index, 1);
            }
        },

        label(id) {
            return _.result(_.find(this.menus, "id", id), "label");
        },

        protected(types) {
            return _.reject(types, { protected: true });
        },

        trash(menus) {
            return _.reject(menus, { id: "trash" });
        },

        divided(menus) {
            return _.reject(menus, { fixed: true }).concat({ divider: true }, _.filter(menus, { fixed: true }));
        },

        menuLabel(id) {
            return this.$trans("(Currently set to: %menu%)", { menu: this.label(id) });
        },

        isFrontpage(node) {
            return node.url === "/";
        },

        type(node) {
            return this.getType(node) || {};
        },

        setFrontpage(node) {
            this.Nodes.save({ id: "frontpage" }, { id: node.id })
                .then(() => {
                    this.load();
                    this.$notify("Frontpage updated.");
                })
                .catch((err) => {
                    this.$notify(err.data, "danger");
                });
        },

        toggleStatus(node) {
            node.status = node.status === 1 ? 0 : 1;

            this.Nodes.save({ id: node.id }, { node }).then(function () {
                this.load();
                this.$notify("Page saved.");
            });
        },
    },

    computed: {
        showDelete() {
            const vm = this;
            return this.showMove && _.every(this.getSelected(), (node) => !(vm.getType(node) || {}).protected, this);
        },

        showMove() {
            return this.isSelected(this.getSelected(), true);
        },

        isMobile() {
            return isMobile(navigator.userAgent).any;
        },
    },

    components: {
        VueNestable,
        VueNestableHandle,
        ValidationObserver,
        VInput,
    },
});
