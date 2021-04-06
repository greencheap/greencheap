var Widgets = {
    name: "widgets",

    el: "#widgets",

    mixins: [window.Widgets, Theme.Mixins.Helper],

    data() {
        return _.merge(
            {
                position: this.$session.get("widget.position"),
                selected: [],
                config: { positions: [], filter: this.$session.get("widget.filter", {}) },
                unassignedWidgets: [],
                type: {},
            },
            window.$data
        );
    },

    mounted() {
        this.load();
    },

    computed: {
        filtered_positions() {
            // replace index.php - positions" track-by="name" v-if="pos | show"
            return _.filter(this.positions, (position) => {
                if (!this.position) {
                    return position.name != "_unassigned" ? position.widgets.length : 0;
                }

                return this.active(position);
            });
        },

        positions() {
            return this.config.positions.concat(this.unassigned);
        },

        unassigned() {
            return {
                name: "_unassigned",
                label: this.$trans("Unassigned"),
                assigned: _.map(this.unassignedWidgets, "id"),
                widgets: this.unassignedWidgets,
            };
        },

        empty() {
            return !this.position && !this.get("assigned").length;
        },

        nodes() {
            const options = [];
            const nodes = _(this.config.nodes).groupBy("menu").value();

            _.forEach(
                this.config.menus,
                (menu, name) => {
                    const opts = nodes[name];

                    if (!opts) {
                        return;
                    }

                    options.push({
                        label: menu.label,
                        options: _.map(opts, (node) => ({ text: node.title, value: node.id })),
                    });
                },
                this
            );

            return options;
        },
    },

    methods: {
        get(filter) {
            const filters = {
                selected(widget) {
                    return this.selected.indexOf(widget.id) !== -1;
                },

                assigned(widget) {
                    return this.positions.some((position) => position.assigned.indexOf(widget.id) !== -1);
                },

                unassigned(widget) {
                    return !this.positions.some((position) => position.assigned.indexOf(widget.id) !== -1);
                },
            };

            return filters[filter] ? this.widgets.filter(filters[filter], this) : this.widgets;
        },

        load() {
            return this.resource.query().then(function (res) {
                this.config.positions = res.data.positions;
                this.unassignedWidgets = res.data.unassigned;
            });
        },

        active(position) {
            return this.position === position || (position && this.position && this.position.name == position.name);
        },

        select(position) {
            if (position) {
                this.$set(this, "selected", []);
            }

            this.$set(this, "position", position);
            if (position) {
                this.$session.set("widget.position", position);
            } else {
                this.$session.remove("widget.position");
            }
        },

        assign(position, ids) {
            return this.resource.save({ id: "assign" }, { position, ids }).then(function () {
                this.load();
                this.$set(this, "selected", []);
            });
        },

        move(position, ids) {
            position = _.find(this.positions, { name: position });

            this.assign(position.name, position.assigned.concat(ids)).then(function () {
                this.$notify(this.$transChoice("{1} %count% Widget moved|]1,Inf[ %count% Widgets moved", ids.length, { count: ids.length }));
            });
        },

        copy() {
            this.resource.save({ id: "copy" }, { ids: this.selected }).then(function (res) {
                this.load().then();
                this.$set(this, "selected", []);
                this.$notify("Widget(s) copied.");
            });
        },

        remove() {
            this.resource.delete({ id: "bulk" }, { ids: this.selected }).then(function () {
                this.load();
                this.$notify("Widget(s) removed.");
                this.$set(this, "selected", []);
            });
        },

        status(status) {
            const widgets = this.get("selected");

            widgets.forEach((widget) => {
                widget.status = status;
            });

            this.resource.save({ id: "bulk" }, { widgets }).then(function () {
                this.load();
                this.$set(this, "selected", []);
                this.$notify("Widget(s) saved.");
            });
        },

        toggleStatus(widget) {
            widget.status = widget.status ? 0 : 1;

            this.resource.save({ id: widget.id }, { widget }).then(function () {
                this.load();
                this.$notify("Widget saved.");
            });
        },

        infilter(widget) {
            if (this.config.filter.search) {
                return widget.title.toLowerCase().indexOf(this.config.filter.search.toLowerCase()) != -1;
            }

            if (this.config.filter.node && widget.nodes.length) {
                return widget.nodes.some(function (node) {
                    return node === this.config.filter.node;
                }, this);
            }

            return true;
        },

        emptyafterfilter(widgets) {
            widgets = widgets || this.config.positions.reduce((result, position) => result.concat(position.widgets), []);

            return !widgets.some(function (widget) {
                return this.infilter(widget);
            }, this);
        },

        getPageFilter(widget) {
            if (!widget.nodes.length) {
                return this.$trans("All");
            }
            if (widget.nodes.length > 1) {
                return this.$trans("Selected");
            }
            return _.find(this.config.nodes, (value, key) => key == widget.nodes[0]).title;
        },

        isSelected(id) {
            return this.selected.indexOf(id) !== -1;
        },

        typeExist(widget) {
            const type = _.find(window.$data.types, { name: widget.type });

            if (!type) {
                return undefined;
            }

            return type.label || type.name;
        },
    },

    watch: {
        "config.filter": {
            handler(filter) {
                this.$session.set("widget.filter", filter);
            },
            deep: true,
        },
    },

    filters: {
        show(position) {
            if (!this.position) {
                return position.name != "_unassigned" ? position.widgets.length : 0;
            }

            return this.active(position);
        },

        type(widget) {
            const type = _.find(window.$data.types, { name: widget.type });

            if (!type) {
                return undefined;
            }

            return type.label || type.name;
        },

        assigned(ids) {
            return ids
                .map(function (id) {
                    return _.find(this.widgets, "id", id);
                }, this)
                .filter((widget) => widget !== undefined);
        },
    },

    directives: {
        sortable: {
            params: ["group"],

            bind(el, binding, vnode) {
                const vm = this;

                // disable sorting on unassigned position
                if (el.getAttribute("data-position") == "_unassigned") {
                    return;
                }

                binding.handler = function (e, sortable, element) {
                    const action = e.type;
                    if (action == "added" || action == "moved") {
                        vnode.context.assign(UIkit.util.data(el, "position"), _.map(binding.def.serialize(el, binding, vnode), "id"));
                    }
                    if (action == "removed") {
                        if (!sortable.$el.children.length) {
                            console.log("!!!");
                            element.remove();
                        }
                    }
                };

                Vue.nextTick(() => {
                    const sortable = UIkit.sortable(el, { group: "position" });
                    binding.sortable = sortable.$el;
                    UIkit.util.on(sortable.$el, "added moved removed", binding.handler);
                });
            },

            unbind(el, binding, vnode) {
                UIkit.util.off(binding.sortable, "added moved removed", binding.handler);
            },

            serialize(el, binding, vnode) {
                const data = [];
                let item;
                let attribute;
                const vm = this;

                UIkit.util.toNodes(el.children).forEach((child, j) => {
                    item = {};
                    for (var i = 0, attr, val; i < child.attributes.length; i++) {
                        attribute = child.attributes[i];
                        if (attribute.name.indexOf("data-") === 0) {
                            attr = attribute.name.substr(5);
                            val = vm.str2json(attribute.value);
                            item[attr] = val || attribute.value == "false" || attribute.value == "0" ? val : attribute.value;
                        }
                    }
                    data.push(item);
                });

                return data;
            },

            str2json(str, notevil) {
                try {
                    if (notevil) {
                        return JSON.parse(
                            str
                                // wrap keys without quote with valid double quote
                                .replace(/([\$\w]+)\s*:/g, (_, $1) => `"${$1}":`)
                                // replacing single quote wrapped ones to double quote
                                .replace(/'([^']+)'/g, (_, $1) => `"${$1}"`)
                        );
                    }
                    return new Function("", `var json = ${str}; return JSON.parse(JSON.stringify(json));`)();
                } catch (e) {
                    return false;
                }
            },
        },
    },
};

export default Widgets;

Vue.ready(Widgets);
