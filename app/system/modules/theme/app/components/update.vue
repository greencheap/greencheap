<template>
    <div v-if="hasUpdate">
        <div class="uk-width-large@s uk-card uk-card-secondary uk-card-body tm-update-position uk-background-cover uk-text-center" :data-src="$url('app/system/modules/theme/assets/images/upload-bg.png')" uk-img>
            <img :data-src="$url('app/system/modules/theme/assets/images/greencheap-logo.svg')" width="100" uk-svg />
            <p>{{ "GreenCheap has released a new update for you. If you want to upgrade your system immediately. Just use the button below" | trans }}</p>
            <a :href="$url('/admin/system/update')" class="uk-button uk-button-primary">{{ "Update Now" | trans }}</a>
        </div>
    </div>
</template>

<script>
import Version from "../../../../../installer/app/lib/version";

export default {
    name: "Update",

    data() {
        return _.merge(
            {
                hasUpdate: false,
            },
            window.$client
        );
    },

    mounted() {
        this.getVersion();
    },

    methods: {
        getVersion() {
            this.clientResource("api/atomy/main_packages/checkversion", {
                stability: this.settings.beta ? "STATUS_BETA" : "STATUS_PUBLISHED",
                php_version: "8.0.*",
                version: this.version,
            }).then((res) => {
                const data = res.data;
                if (Version.compare(this.version, data.version, "<")) {
                    this.hasUpdate = data;
                    return;
                }
                this.hasUpdate = false;
            });
        },

        clientResource(url, parameter) {
            const urlParam = `${this.system_api}/${url}`;
            if (!parameter) {
                parameter = {};
            }
            const http = this.$http.get(urlParam, {
                params: parameter,
            });
            return http;
        },
    },
};
</script>
