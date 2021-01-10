<template>
    <div v-if="hasUpdate" class="uk-section uk-section-muted uk-section-xsmall uk-background-image uk-background-cover" :data-src="$url.route('app/system/modules/theme/assets/images/update-bg.jpg')" uk-img>
        <div class="uk-container">
            <div class="uk-flex-middle" uk-grid>
                <div class="uk-width-expand@s uk-flex-left@s uk-flex-center">
                    <h3 class="uk-h5 uk-text-bold uk-margin-remove uk-light">{{ 'GreenCheap posted %version% version update. Now you can update your system to the latest version!' | trans({version:'1.3.2'}) }}</h3>
                </div>
                <div class="uk-width-medium@s uk-flex-right@s uk-flex-center">
                    <a :href="$url.route('admin/system/update')" class="uk-margin-remove uk-button uk-button-primary">{{ 'Update to new version' | trans }}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Version from '../../../../../installer/app/lib/version';
    import Client from '../../../../app/lib/client';
    export default {
        name: 'Update',

        data(){
            return {
                hasUpdate: false,
                checked: this.$session.get('checked_version' , false)
            }
        },

        mixins: [Client],

        created(){
            if(!this.checked){
                this.getVersion()
            }
        },

        methods:{
            getVersion() {
                this.$session.set('checked_version' , true);
                this.clientResource('version/get', {
                    constraint: this.settings.beta ? 'beta' : 'stable',
                }).then((res) => {
                    const data = res.data.version;
                    if (Version.compare(this.version, data.version, '<')) {
                        this.hasUpdate = data;
                        return;
                    }
                    this.hasUpdate = false;
                })
            },

        }
    }
</script>
