<template>
    <div>
        <div ref="input" class="package-upload">
            <div uk-form-custom>
                <input type="file" name="file" />
                <button class="uk-button uk-button-primary" type="button" tabindex="-1">
                    <span v-if="!progress">{{ 'Upload' | trans }}</span>
                    <span v-else><i uk-spinner /> {{ progress }}</span>
                </button>
            </div>
        </div>

        <v-modal ref="modal">
            <package-details :api="api" :package="package" />

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">
                    {{ 'Cancel' | trans }}
                </button>
                <button class="uk-button uk-button-primary" @click.prevent="doInstall">
                    {{ 'Install' | trans }}
                </button>
            </div>
        </v-modal>
    </div>
</template>

<script>

import Package from '../lib/package';
import PackageDetails from './package-details.vue';

export default {

    mixins: [Package, Theme.Mixins.Helper],

    props: {
        api: { type: String, default: '' },
        packages: Array,
        type: String,
    },

    data() {
        return {
            package: {},
            upload: null,
            progress: '',
        };
    },

    theme: {
        elements() {
            var vm = this;
            return {
                'upload': {
                    scope: 'topmenu',
                    type: 'button',
                    caption: () => !vm.progress ? 'Upload' : vm.progress,
                    class: 'uk-button uk-button-primary',
                    icon: {
                        attrs: {
                            'uk-spinner': '',
                            ratio: '.6'
                        },
                        class: 'uk-margin-small-right',
                        vif: () => vm.progress
                    },
                    on: {
                        click: () => {
                            return UIkit.util.$('input', vm.$refs.input).click();
                        },
                    },
                    priority: 0,
                }
            }
        }
    },

    mounted() {
        const { type } = this;
        const settings = {
            url: this.$url.route('admin/system/package/upload'),
            dataType: 'json',
            name: 'file',
            beforeAll(options) {
                _.merge(options.params, { _csrf: $greencheap.csrf, type });
            },
            loadStart: this.onStart,
            progress: this.onProgress,
            completeAll: this.onComplete,
        };

        UIkit.upload(this.$refs.input, settings);
    },

    methods: {

        onStart() {
            this.progress = '1%';
        },

        onProgress(percent) {
            this.progress = `${Math.ceil(percent.loaded / percent.total * 100)}%`;
        },

        onComplete(data) {
            try {
                // var data = $.parseJSON(data.responseText);
                var data = JSON.parse(data.responseText);
            } catch (e) {
                try {
                    var data = JSON.parse(data.responseText.substring(data.responseText.lastIndexOf('{'), data.responseText.lastIndexOf('}') + 1));
                    var { message } = data;
                } catch (e) {
                    var message = 'Unable load package.';
                }
                this.progress = '';
                this.$notify(message, 'danger');
                return;
            }

            const vm = this;

            this.progress = '100%';

            setTimeout(() => {
                vm.progress = '';
            }, 250);

            if (!data.package) {
                this.$notify(data, 'danger');
                return;
            }

            this.$set(this, 'upload', data);
            this.$set(this, 'package', data.package);

            this.$refs.modal.open();
        },

        doInstall() {
            this.$refs.modal.close()
            this.install(this.upload.package, this.packages,
                (output) => {
                    if (output.status === 'success') {
                        setTimeout(() => {
                            location.reload();
                        }, 300);
                    }
                }, true);
        },

    },

    components: {
        'package-details': PackageDetails
    },
};

</script>
