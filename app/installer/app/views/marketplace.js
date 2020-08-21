import Version from '../lib/version';
import Package from '../lib/package';

const marketplace = {
    el: '#marketplace',
    name: 'Marketplace',
    data() {
        return _.merge({
            pkgs: false,
            config: {
                filter: this.$session.get('pkgs-front.filter', { order: 'download_count desc', type: 'greencheap-extension' }),
            },
            pages: 0,
            count: '',
            modalpkg: false,
            client: window.$client,
        }, window.$data);
    },

    mixins: [
        Package,
        Theme.Mixins.Helper,
        // eslint-disable-next-line global-require
        require('../../../system/app/lib/client'),
    ],

    mounted() {
        this.$watch('config.page', this.load, { immediate: true });
    },

    watch: {
        'config.filter': {
            handler(filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }
                this.$session.set('pkgs-front.filter', filter);
            },
            deep: true,
        },

        pkgs: {
            handler() {
                _.forEach(this.pkgs, (pkg, key) => {
                    const controll = _.find(this.installedPackages, (installedPkg) => {
                        if (installedPkg.name === pkg.package_name) {
                            return true;
                        }
                        return false;
                    });
                    this.pkgs[key] = _.merge({
                        installed: controll ? {
                            name: controll.name,
                            version: controll.version,
                            type: controll.type,
                            update: this.checkVersion(pkg.version, controll.version, '>'),
                        } : false,
                    }, this.pkgs[key]);
                });
            },
            deep: true,
        },
    },

    methods: {
        load() {
            this.clientResource('marketplace/getfilters', {
                filter: this.config.filter, page: this.config.page,
            }).then((res) => {
                const { data } = res;
                this.$set(this, 'pkgs', data.packages);
                this.$set(this, 'pages', data.pages);
                this.$set(this, 'count', data.count);
            }).catch((err) => {
                this.$notify(err.body.error, 'danger');
            });
        },

        setType(type) {
            this.config.filter.type = type;
        },

        checkVersion(version, version2, operator) {
            return Version.compare(version, version2, operator);
        },

        openModal(pkg) {
            this.$set(this, 'modalpkg', pkg);
            this.$refs.modalDeatil.open();
        },

        getImage(data) {
            return data.image.src;
        },

        downloadPackage() {
            this.$http.get('admin/system/package/downloadpackage', {
                params: {
                    id: this.modalpkg.id,
                    type: this.modalpkg.type,
                },
            }).then((res) => {
                const pkg = res.data.package;
                this.doInstall(pkg);
            }).catch((err) => {
                this.$notify(err.bodyText, 'danger');
            });
        },

        doInstall(pkg) {
            this.$refs.modalDeatil.close();
            this.install(pkg,
                (output) => {
                    if (output.status === 'success') {
                        setTimeout(() => {
                            location.reload();
                        }, 300);
                    }
                }, true);
        },
    },
};

export default marketplace;

Vue.ready(marketplace);
