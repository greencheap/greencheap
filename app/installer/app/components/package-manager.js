import Package from '../lib/package';
import PackageUpload from './package-upload.vue';
import PackageDetails from './package-details.vue';

export default {

    mixins: [Package,Theme.Mixins.Helper],

    data() {
        return _.extend({
            package: {},
            view: false,
            updates: null,
            search: this.$session.get(`${this.$options.name}.search`, ''),
            status: '',
        }, window.$data);
    },

    mounted() {
        // Marketplace tarafında API kontrol sağlıyor.
        //this.load();
    },

    watch: {
        search(search) {
            this.$session.set(`${this.$options.name}.search`, search);
        },
    },

    methods: {

        load() {
            this.$set(this, 'status', 'loading');

            if (this.packages) {
                this.queryUpdates(this.packages).then(function (res) {
                    const data = res;
                    this.$set(this, 'updates', data.packages.length ? _.keyBy(data.packages, 'name') : null);
                    this.$set(this, 'status', '');
                }, function () {
                    this.$set(this, 'status', 'error');
                });
            }
        },

        icon(pkg) {
            if (pkg.extra && pkg.extra.icon) {
                return `${pkg.url}/${pkg.extra.icon}`;
            }
            return this.$url('app/system/assets/images/placeholder-icon.svg');
        },

        image(pkg) {
            if (pkg.extra && pkg.extra.image) {
                return `${pkg.url}/${pkg.extra.image}`;
            }
            return this.$url('app/system/assets/images/placeholder-800x600.svg');
        },

        details(pkg) {
            this.$set(this, 'package', pkg);
            this.$refs.details.open();
        },

        settings(pkg) {
            if (!pkg.settings) {
                return;
            }

            let view;
            let options;

            _.forIn(this.$options.components, (component, name) => {
                options = component.options || {};
                if (component.settings && pkg.settings === name) {
                    view = name;
                }
            });
            if (view) {
                this.$set(this, 'package', pkg);
                this.$set(this, 'view', view);
                this.$refs.settings.open();
            } else {
                window.location = pkg.settings;
            }
        },

        empty(packages) {
            return this.filterBy(packages, this.search, 'title').length === 0;
        },

    },

    components: {
        'package-upload': PackageUpload,
        'package-details': PackageDetails,
    },

};
