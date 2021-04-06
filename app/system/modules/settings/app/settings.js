window.Settings = {
    name: "settings",

    el: "#settings",

    mixins: [Theme.Mixins.Helper, Theme.Mixins.Elements],

    data() {
        return _.extend(
            {
                keysDict: {},
            },
            window.$settings
        );
    },

    created() {
        this.toHtml5Keys(this.config, this.options);
        this.$theme.$tabs("leftTab", "#settings .uk-nav", { connect: ".settings-tab", state: true });
    },

    mounted() {
        // UIkit.switcher(this.$refs.tab, { connect: '.settings-tab' });
    },

    computed: {
        sections() {
            const sections = [];

            _.forIn(this.$options.components, (component, name) => {
                const { section } = component;

                if (section) {
                    section.name = name;
                    sections.push(section);
                }
            });

            return sections;
        },
    },

    methods: {
        toHtml5Keys() {
            const vm = this;
            Array.from(arguments).forEach((obj) => {
                Object.keys(obj).forEach((key) => {
                    if (key.indexOf("/") != -1) {
                        new_key = key.replace(/\//g, "-");
                        vm.keysDict[new_key] = key;
                        obj[new_key] = obj[key];
                        delete obj[key];
                    }
                });
            });
        },

        toOrigin(data) {
            const vm = this;
            const origin = _.extend({}, data);
            Object.keys(origin).forEach((key) => {
                if (vm.keysDict.hasOwnProperty(key)) {
                    origin[vm.keysDict[key]] = origin[key];
                    delete origin[key];
                }
            });

            return origin;
        },

        save() {
            this.$trigger("save:settings", this.$data);

            this.$resource("admin/system/settings/save")
                .save({ config: this.toOrigin(this.config), options: this.toOrigin(this.options) })
                .then(
                    function () {
                        this.$notify("Settings saved.");
                    },
                    function (res) {
                        this.$notify(res.data, "danger");
                    }
                );
        },
    },

    components: {
        locale: require("./components/locale.vue").default,
        system: require("./components/system.vue").default,
        misc: require("./components/misc.vue").default,
    },
};

Vue.ready(window.Settings);
