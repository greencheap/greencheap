<template>

    <v-modal ref="output" :options="options">
        <div class="uk-modal-header uk-flex uk-flex-middle">
            <h2>{{ 'Installing %title% %version%' | trans({title:pkg.title,version:pkg.version}) }}</h2>
        </div>

        <div class="uk-modal-body">

            <pre v-show="showOutput" class="pk-pre uk-text-break" v-html="output" uk-overflow-auto />

            <div v-show="status == 'success'" class="uk-alert uk-alert-success uk-margin-remove">{{ 'Successfully installed.' | trans }}</div>
            <div v-show="status == 'error'" class="uk-alert uk-alert-danger uk-margin-remove">{{ 'Error' | trans }}</div>

        </div>

        <div v-show="status == 'loading'" class="uk-modal-footer uk-text-left">
            <i uk-spinner></i>
            <span class="uk-margin-left">{{'Wait' | trans}}</span>
        </div>

        <div v-show="status != 'loading'" class="uk-modal-footer uk-text-right">
            <a class="uk-button uk-button-text uk-margin-right" @click.prevent="close">{{ 'Close' | trans }}</a>
            <a v-show="status == 'success'" class="uk-button uk-button-primary" @click.prevent="enable">{{ 'Enable' | trans }}</a>
        </div>
    </v-modal>

</template>

<script>

import Output from './output';

export default {

    mixins: [Output],

    methods: {

        install(pkg, packages, onClose, packagist) {
            this.$set(this, 'pkg', pkg);
            this.cb = onClose;

            self = this;
           
            return this.$http.post('admin/system/package/install', {package: pkg, packagist: Boolean(packagist)}, {
                progress(e) {
                    if (e.lengthComputable) {
                        self.init(); 
                    }
                }
            }).then((res) => {
                this.scrollToEnd();
                
                const patt = new RegExp('^status=(.+)' , 'gm');
                this.setOutput(res.bodyText);
                const getStatusTest = patt.exec(res.bodyText);
                this.status = getStatusTest[1];

                if (this.status === 'success' && packages) {
                    const index = _.findIndex(packages, { name: pkg.name });
                    if (index !== -1) {
                        packages.splice(index, 1, pkg);
                    } else {
                        packages.push(pkg);
                    }
                }
            }).catch((err)=>{
                this.$notify(err.data, 'danger');
                this.close();
            });
        },

        enable() {
            this.$parent.enable(this.pkg);
            this.close();
        },
    }

};

</script>
