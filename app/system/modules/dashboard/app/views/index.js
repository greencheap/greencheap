import UIkit from "uikit";
import { $, on, css, append, addClass, removeClass, hasClass, attr, toNodes, each, find, findAll } from "uikit-util";
import Panel from "../components/widget-panel.vue";
import Feed from "../components/widget-feed.vue";
import Location from "../components/widget-location.vue";

import Update from "../../../theme/app/components/update.vue";

import Draggable from "vuedraggable";

window.Dashboard = {
    name: "dashboard",

    el: "#dashboard",

    data() {
        return _.extend(
            {
                editing: {},
                isMove: false,
            },
            window.$data
        );
    },

    watch: {
        widgets: {
            handler() {},
            deep: true,
        },
    },

    created() {
        let widgets = this.widgets;
        if (!widgets.length) {
            widgets = {
                0: [],
                1: [],
                2: [],
            };
            this.$set(this, "widgets", widgets);
        }

        this.Widgets = this.$resource("admin/dashboard{/id}");
    },

    methods: {
        add(type) {
            this.widgets[0].push(_.merge({ type: type.id, unix_id: Date.now() }, type.defaults));
            this.save();
            return false;
        },

        save() {
            this.Widgets.save({ widgets: this.widgets })
                .then((res) => {
                    this.$set(this, "widgets", res.body);
                    this.$notify(this.$trans("Saved"), "primary");
                })
                .catch((err) => {
                    this.$notify(err.bodyText);
                });
        },

        remove(unix_id) {
            _.filter(this.widgets, (column, columnIndex) => {
                _.filter(column, (widget, widgetIndex) => {
                    if (widget.unix_id == unix_id) {
                        this.widgets[columnIndex].splice(widgetIndex, 1);
                        this.save();
                    }
                });
            });
        },

        getType(id) {
            return _.find(this.getTypes(), { id });
        },

        getTypes() {
            const types = [];

            _.forIn(this.$options.components, (component, name) => {
                const { type } = component;

                if (type) {
                    type.component = name;
                    types.push(type);
                }
            });

            return types;
        },

        startMove() {
            this.isMove = true;
        },

        stopMove() {
            this.isMove = false;
            this.save();
        },
    },

    components: {
        draggable: Draggable,
        panel: Panel,
        feed: Feed,
        location: Location,
        Update,
    },
};

Vue.ready(window.Dashboard);
