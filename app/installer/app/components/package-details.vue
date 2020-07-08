<template>
    <div class="package-details">
        <div class="uk-modal-header uk-flex uk-flex-middle">
            <img
                v-if="package.extra && package.extra.icon"
                class="uk-margin-right"
                width="50"
                height="50"
                :alt="package.title"
                :src="package | icon"
            >

            <div class="uk-flex-1">
                <h2 class="uk-margin-remove">
                    {{ package.title }}
                </h2>
                <ul class="uk-subnav uk-subnav-divider uk-margin-remove-bottom uk-margin-remove-top">
                    <li v-if="package.authors && package.authors[0]" class="uk-text-muted">
                        {{ package.authors[0].name }}
                    </li>
                    <li class="uk-text-muted">
                        {{ 'Version %version%' | trans({version:package.version}) }}
                    </li>
                </ul>
            </div>
        </div>

        <div v-show="messages.checksum" class="uk-alert uk-alert-danger uk-margin-remove">
            {{ 'The checksum of the uploaded package does not match the one from the marketplace. The file might be manipulated.' | trans }}
        </div>

        <div v-show="messages.update" class="uk-alert uk-alert-primary uk-margin-remove">
            {{ 'There is an update available for the package.' | trans }}
        </div>

        <div class="uk-modal-body">
            <p v-if="package.description">
                {{ package.description }}
            </p>

            <ul class="uk-list">
                <li v-if="package.license">
                    <strong>{{ 'License:' | trans }}</strong> {{ package.license }}
                </li>
                <template v-if="package.authors && package.authors[0]">
                    <li v-if="package.authors[0].homepage">
                        <strong>{{ 'Homepage:' | trans }}</strong>
                        <a :href="package.authors[0].homepage" target="_blank">{{ package.authors[0].homepage }}</a>
                    </li>
                    <li v-if="package.authors[0].email">
                        <strong>{{ 'Email:' | trans }}</strong>
                        <a :href="'mailto:' + package.authors[0].email">{{ package.authors[0].email }}</a>
                    </li>
                </template>
            </ul>

            <img v-if="package.extra && package.extra.image" width="1200" height="800" :alt="package.title" :src="package | image">
        </div>
    </div>
</template>

<script>

import Version from '../lib/version';

export default {

    props: {
        api: {
            type: String,
            default: '',
        },
        package: {
            type: Object,
            default() {
                return {};
            },
        },
    },

    data() {
        return {
            messages: {},
        };
    },

    filters: {

        icon(pkg) {
            const extra = pkg.extra || {};

            if (!extra.icon) {
                return this.$url('app/system/assets/images/placeholder-icon.svg');
            } if (!extra.icon.match(/^(https?:)?\//)) {
                return `${pkg.url}/${extra.icon}`;
            }

            return extra.icon;
        },

        image(pkg) {
            const extra = pkg.extra || {};

            if (!extra.image) {
                return this.$url('app/system/assets/images/placeholder-image.svg');
            } if (!extra.image.match(/^(https?:)?\//)) {
                return `${pkg.url}/${extra.image}`;
            }

            return extra.image;
        },

    },

    watch: {

        package: {

            handler() {
                if (!this.package.name) {
                    return;
                }

                if (_.isArray(this.package.authors)) {
                    this.package.author = this.package.authors[0];
                }

                this.$set(this, 'messages', {});

                this.queryPackage(this.package, function (res) {
                    const { data } = res;

                    let { version } = this.package;
                    const pkg = data.versions[version];

                    // verify checksum
                    if (pkg && this.package.shasum) {
                        this.$set(this.messages, 'checksum', pkg.dist.shasum != this.package.shasum);
                    }

                    // check version
                    _.each(data.versions, (pkg) => {
                        if (Version.compare(pkg.version, version, '>')) {
                            version = pkg.version;
                        }
                    });

                    this.$set(this.messages, 'update', version != this.package.version);
                });
            },

            immediate: true,

        },
    },

    methods: {

        queryPackage(pkg, success) {
            return this.$http.get(`${this.api}/api/package/{+name}`, {
                params: {
                    name: _.isObject(pkg) ? pkg.name : pkg,
                },
            }).then(success, this.error);
        },

    },

};

</script>
