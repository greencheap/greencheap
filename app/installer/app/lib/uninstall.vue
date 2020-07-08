<template>

    <v-modal ref="output" :options="options">
        <div class="uk-modal-header uk-flex uk-flex-middle">
            <h2>{{ 'Removing %title% %version%' | trans({title:pkg.title,version:pkg.version}) }}</h2>
        </div>

        <div class="uk-modal-body">
            <pre v-show="showOutput" class="pk-pre uk-text-break" v-html="output" uk-overflow-auto />
            <div v-show="status == 'success'" class="uk-alert uk-alert-success uk-margin-remove">{{ 'Successfully removed.' | trans }}</div>
            <div v-show="status == 'error'" class="uk-alert uk-alert-danger uk-margin-remove">{{ 'Error' | trans }}</div>
        </div>

        <div v-show="status == 'loading'" class="uk-modal-footer uk-text-left">
            <i uk-spinner></i>
            <span class="uk-margin-left">{{'Wait' | trans}}</span>
        </div>

        <div v-show="status != 'loading'" class="uk-modal-footer uk-text-right">
            <a class="uk-button uk-button-secondary" @click.prevent="closeSecond">{{ 'Close' | trans }}</a>
        </div>
    </v-modal>

</template>

<script>

import Output from './output';

export default {

    mixins: [Output],

    methods: {

        uninstall(pkg, packages) {
            this.$set(this, 'pkg', pkg);
            self = this;
            return this.$http.post('admin/system/package/uninstall' , { name: pkg.name } , {
                progress(e) {
                    if (e.lengthComputable) {
                        self.init(); 
                    }
                }
            }).then((res)=>{
                this.scrollToEnd();
                const patt = new RegExp('^status=(.+)' , 'gm');
                this.setOutput(res.bodyText);
                const getStatusTest = patt.exec(res.bodyText);
                this.status = getStatusTest[1];

                if (this.status === 'success' && packages) {
                    packages.splice(packages.indexOf(pkg), 1);
                }
            }).catch((err)=>{
                this.$notify(err.data, 'danger');
                this.close();
            });
        },

    },

};

</script>
