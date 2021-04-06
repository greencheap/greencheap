"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true,
});
exports["default"] = void 0;

var _package = _interopRequireDefault(require("../lib/package"));

var _packageUpload = _interopRequireDefault(require("./package-upload.vue"));

var _packageDetails = _interopRequireDefault(require("./package-details.vue"));

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : { default: obj };
}

var _default = {
    mixins: [_package["default"], Theme.Mixins.Helper],
    data: function data() {
        return _.extend(
            {
                package: {},
                view: false,
                updates: null,
                search: this.$session.get("".concat(this.$options.name, ".search"), ""),
                status: "",
            },
            window.$data
        );
    },
    mounted: function mounted() {
        //this.load();
    },
    watch: {
        search: function search(_search) {
            this.$session.set("".concat(this.$options.name, ".search"), _search);
        },
    },
    methods: {
        load: function load() {
            this.$set(this, "status", "loading");

            if (this.packages) {
                this.queryUpdates(this.packages).then(
                    function (res) {
                        var data = res;
                        this.$set(this, "updates", data.packages.length ? _.keyBy(data.packages, "name") : null);
                        this.$set(this, "status", "");
                    },
                    function () {
                        this.$set(this, "status", "error");
                    }
                );
            }
        },
        icon: function icon(pkg) {
            if (pkg.extra && pkg.extra.icon) {
                return "".concat(pkg.url, "/").concat(pkg.extra.icon);
            }

            return this.$url("app/system/assets/images/placeholder-icon.svg");
        },
        image: function image(pkg) {
            if (pkg.extra && pkg.extra.image) {
                return "".concat(pkg.url, "/").concat(pkg.extra.image);
            }

            return this.$url("app/system/assets/images/placeholder-800x600.svg");
        },
        details: function details(pkg) {
            this.$set(this, "package", pkg);
            this.$refs.details.open();
        },
        settings: function settings(pkg) {
            if (!pkg.settings) {
                return;
            }

            var view;
            var options;

            _.forIn(this.$options.components, function (component, name) {
                options = component.options || {};

                if (component.settings && pkg.settings === name) {
                    view = name;
                }
            });

            if (view) {
                this.$set(this, "package", pkg);
                this.$set(this, "view", view);
                this.$refs.settings.open();
            } else {
                window.location = pkg.settings;
            }
        },
        empty: function empty(packages) {
            return this.filterBy(packages, this.search, "title").length === 0;
        },
    },
    components: {
        "package-upload": _packageUpload["default"],
        "package-details": _packageDetails["default"],
    },
};
exports["default"] = _default;
