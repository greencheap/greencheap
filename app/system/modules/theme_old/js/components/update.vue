<template>
    <div>
        <div v-if="hasUpdate">
            <button class="uk-button uk-button-default uk-button-large" @click.prevent="openModal">
                <i uk-icon="git-fork" />
                <span>{{ 'Update Released' | trans }}</span>
            </button>
        </div>

        <div id="versionStableModal" class="uk-flex-top" uk-modal="bg-close:false;esc-close:false">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-header">
                    <h2 class="uk-modal-title">
                        {{ 'Update Released' | trans }}
                    </h2>
                    <hr>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-margin">
                        <div v-if="hasUpdate && !output">
                            <div class="uk-height-medium" uk-overflow-auto>
                                <div class="uk-background uk-background-muted uk-padding-small">
                                    <div>
                                        <div v-html="$options.filters.markdown(hasUpdate.readme)" />
                                        <hr>
                                        <h1>{{ 'Changelog' | trans }}</h1>
                                        <div class="uk-grid-small" uk-grid v-html="changelog(hasUpdate.changelog)" />
                                    </div>
                                </div>
                            </div>

                            <ul class="uk-list uk-list-divider">
                                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                    <span class="uk-margin-small-right">{{ 'YOUR VERSION' | trans }}</span> <strong>{{ version }}</strong>
                                </li>
                                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                    <span class="uk-margin-small-right">{{ 'NEW VERSION' | trans }}</span> <strong>{{ hasUpdate.version }}</strong>
                                </li>
                            </ul>
                        </div>
                        <pre v-show="output" class="uk-margin" v-html="output" />
                        <progress v-show="modal.progressbar > 0" class="uk-progress" :value="modal.progressbar" max="100" />
                    </div>
                </div>
                <div class="uk-modal-footer uk-position-relative uk-text-right">
                    <hr>
                    <div v-show="modal.isLoader" class="uk-position-absolute uk-position-center-left uk-position-medium" style="bottom:-50px">
                        <i uk-spinner />
                        <span class="uk-margin-left">{{ 'Wait' | trans }}</span>
                    </div>
                    <a v-show="finished" :class="'uk-button uk-button-primary'" :href="$url.route('admin/dashboard')">{{ 'Finish Update' | trans }}</a>
                    <button v-show="modal.button.cancel.isShow || !finished" class="uk-button uk-button-default" type="button" :disabled="modal.button.cancel.isDisabled" @click.prevent="closeModal">
                        {{ modal.button.cancel.text | trans }}
                    </button>
                    <button v-show="modal.button.accept.isShow || !finished" class="uk-button uk-button-primary" type="button" :disabled="modal.button.accept.isDisabled" @click.prevent="doDownload">
                        {{ modal.button.accept.text | trans }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Version from '../../../../../installer/app/lib/version';

export default {
    data() {
        return {
            hasUpdate: false,
            uikitModal: null,
            newPackage: false,
            output: '',
            status: 'success',
            finished: false,
            modal: {
                button: {
                    accept: {
                        text: 'Install',
                        isDisabled: false,
                        isShow: true,
                    },
                    cancel: {
                        text: 'Cancel',
                        isDisabled: false,
                        isShow: true,
                    },
                },
                isLoader: false,
                progressbar: 0,
            },
        };
    },

    watch: {
        'modal.isLoader': {
            handler(newValue) {
                if (newValue) {
                    this.modal.button.cancel.isDisabled = newValue;
                    this.modal.button.accept.isDisabled = newValue;
                }
            },
            deep: true,
        },
    },

    // eslint-disable-next-line vue/order-in-components
    mixins: [
        // eslint-disable-next-line global-require
        require('../../../../app/lib/client'),
    ],

    created() {
        this.getVersion();
    },

    mounted() {
        this.uikitModal = UIkit.modal('#versionStableModal');
    },

    methods: {
        openModal() {
            this.uikitModal.show();
        },

        getVersion() {
            this.clientResource('version/get', {
                constraint: this.settings.beta ? 'beta' : 'stable',
            }).then((res) => {
                const data = res.data.version;
                if (Version.compare(this.version, data.version, '<')) {
                    this.hasUpdate = data;
                    return;
                }
                this.hasUpdate = false;
            }).catch((err) => {
                this.$notify(err.body.error, 'danger');
            });
        },

        doDownload() {
            this.output = '';
            this.output += 'Server Side Connecting..\n';
            setTimeout(() => {
                this.output += 'New version downloading\n';
                this.modal.progressbar = 40;
            }, 2000);
            const ref = this;
            this.modal.isLoader = true;
            this.modal.progressbar = 1;
            this.$http.get('admin/system/update/api/download-release', {
                params: {
                    constraint: this.settings.beta ? 'beta' : 'stable',
                },
            }, {
                progress(e) {
                    if (e.lengthComputable) {
                        ref.modal.progressbar = (e.loaded / e.total) * 50;
                    }
                },
            }).then(() => {
                this.output += 'Finish Download...\n\n';
                this.modal.progressbar = 78;
                this.doInstall();
            }).catch((err) => {
                this.$notify(err.bodtText, 'danger');
                this.closeModal();
            });
        },

        doInstall() {
            this.output += 'Starting Update...\n\n';
            this.modal.progressbar = 79;
            this.modal.isLoader = true;
            this.$http.get('admin/system/update/update', null).then((res) => {
                this.setOutput(res.bodyText);
                this.doMigration();
            }).catch(() => {
                this.error();
            });
        },

        doMigration() {
            this.modal.progressbar = 100;
            if (this.status === 'success') {
                this.$http.get('admin/system/migration/migrate').then((res) => {
                    const { data } = res;
                    this.output += `\n\n${data.status}`;
                    this.finished = true;
                    this.modal.isLoader = false;
                    this.modal.button.cancel.isShow = false;
                    this.modal.button.accept.isShow = false;
                }, this.error);
            } else {
                this.error();
            }
        },

        closeModal() {
            this.modal.button.cancel.isDisabled = true;
            this.modal.button.accept.isDisabled = true;
            this.modal.isLoader = true;
            const ref = this;
            setTimeout(() => {
                ref.modal.button.cancel.isDisabled = false;
                ref.modal.button.accept.isDisabled = false;
                ref.modal.isLoader = false;
                ref.modal.progressbar = 0;
                ref.uikitModal.hide();
            }, 2000);
        },

        setOutput(output) {
            const lines = output.split('\n');
            const match = lines[lines.length - 1].match(/^status=(success|error)$/);
            if (match) {
                // eslint-disable-next-line prefer-destructuring
                this.status = match[1];
                delete lines[lines.length - 1];
                this.output += lines.join('\n');
            } else {
                this.output += output;
            }
        },

        error() {
            this.$notify(this.$trans('Whoops, something went wrong.'));
            this.deletePackage();
        },

        abort(msg) {
            this.modal.isLoader = false;
            this.$notify(msg, 'danger');
            this.modal.progressbar = 0;
        },
    },
};
</script>
