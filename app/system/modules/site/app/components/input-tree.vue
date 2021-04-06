<template>
    <div>
        <label
            ><input v-model="all" class="uk-checkbox" type="checkbox" /><span class="uk-margin-small-left">{{ "All Pages" | trans }}</span></label
        >

        <ul class="uk-list">
            <li v-for="(menu, key) in treelist" v-show="menu.list.length" :key="key">
                <span class="uk-h5">{{ menu.label }}</span>
                <vue-nestable :value="menu.list">
                    <label slot-scope="{ item }" :item="item"
                        ><input v-model="c_active" class="uk-checkbox" type="checkbox" :value="item.id" number /><span class="uk-margin-small-left">{{ item.title }}</span></label
                    >
                </vue-nestable>
            </li>
        </ul>
    </div>
</template>

<script>
import { VueNestable } from "vue-nestable";

export default {
    name: "input-tree",

    props: {
        trash: {
            type: Boolean,
            default: false,
        },
        active: {
            type: Array,
            default() {
                return [];
            },
        },
    },

    data() {
        return {
            menus: [],
            nodes: [],
            all: true,
            c_active: [],
            treelist: [],
        };
    },

    created() {
        this.c_active = this.active;
        this.all = !this.active || !this.active.length;
    },

    watch: {
        c_active(active) {
            this.all = !active || !active.length;
            this.$emit("input", active);
        },

        all(all) {
            if (all) {
                this.c_active = [];
            }
        },
    },

    mounted() {
        const vm = this;

        Vue.Promise.all([this.$http.get("api/site/node"), this.$http.get("api/site/menu")]).then(
            (responses) => {
                vm.$set(vm, "nodes", responses[0].data);
                vm.$set(vm, "menus", vm.trash ? responses[1].data : _.reject(responses[1].data, { id: "trash" }));

                _.forEach(vm.menus, (menu) => {
                    const nodes = _(vm.nodes).filter({ menu: menu.id }).sortBy("priority").value();
                    vm.treelist.push({ label: menu.label, list: vm.unflatten(nodes) });
                });
            },
            () => {
                vm.$notify("Could not load config.", "danger");
            }
        );
    },

    methods: {
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
    },

    computed: {
        grouped() {
            return _(this.nodes)
                .groupBy("menu")
                .mapValues((nodes) =>
                    _(nodes || {})
                        .sortBy("priority")
                        .groupBy("parent_id")
                        .value()
                )
                .value();
        },
    },

    components: {
        VueNestable,
    },
};

Vue.component("input-tree", (resolve) => {
    resolve(require("./input-tree.vue"));
});
</script>
