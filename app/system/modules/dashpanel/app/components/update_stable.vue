<template>
    <div>
        <div v-if="system.isClientSuitable && hasUpdate">
            <a @click.prevent="openModal" class="update tm-text-small update-hover">
                <i class="hasupdateanimation"></i>
                <span>{{'Update Released' | trans}}</span>
            </a>
        </div>

        <div id="versionStableModal" class="uk-flex-top" uk-modal="bg-close:false;esc-close:false">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-header">
                    <h2 class="uk-modal-title">{{'Update Released' | trans}}</h2>
                    <hr>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-margin">
                        <div v-if="hasUpdate && !output">
                            <div class="uk-height-medium" uk-overflow-auto>
                                <div class="uk-background uk-background-muted uk-padding-small">
                                    <div>
                                        <div v-html="$options.filters.markdown(hasUpdate.readme)"></div>
                                        <hr>
                                        <h1>{{'Changelog' | trans}}</h1>
                                        <div class="uk-grid-small" uk-grid v-html="changelog(hasUpdate.changelog)"></div>
                                    </div>
                                </div>
                            </div>

                            <ul class="uk-list uk-list-divider">
                                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                    <span class="uk-margin-small-right">{{'YOUR VERSION' | trans}}</span> <strong>{{system.version.full}}</strong>
                                </li>
                                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                    <span class="uk-margin-small-right">{{'NEW VERSION' | trans}}</span> <strong>{{hasUpdate.full}}</strong>
                                </li>
                                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                    <span class="uk-margin-small-right">{{'CONSTRAINT' | trans}}</span> <strong class="uk-text-primary uk-text-lowercase">{{hasUpdate.constraint}}</strong>
                                </li>
                            </ul>
                        </div>
                        <pre class="uk-margin" v-html="output" v-show="output"></pre>
                        <progress v-show="modal.progressbar > 0" class="uk-progress" :value="modal.progressbar" max="100"></progress>
                    </div>
                </div>
                <div class="uk-modal-footer uk-position-relative uk-text-right">
                    <hr>
                    <div class="uk-position-absolute uk-position-center-left uk-position-medium" style="bottom:-50px" v-show="modal.isLoader">
                        <i uk-spinner></i>
                        <span class="uk-margin-left">{{'Wait' | trans}}</span>
                    </div>
                    <a :class="'uk-button uk-button-primary'" :href="$url.route('admin/dashboard')" v-show="finished">{{ 'Finish Update' | trans }}</a>
                    <button @click.prevent="closeModal" class="uk-button uk-button-default" type="button" :disabled="modal.button.cancel.isDisabled" v-show="modal.button.cancel.isShow || !finished">{{modal.button.cancel.text | trans}}</button>
                    <button @click.prevent="doDownload" class="uk-button uk-button-primary" type="button" :disabled="modal.button.accept.isDisabled" v-show="modal.button.accept.isShow || !finished">{{modal.button.accept.text | trans}}</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import Version from '../../../../../installer/app/lib/version';
    export default {
        section:{
            label:'Update Stable',
            priority:-999
        },

        data(){
            return {
                hasUpdate:false,
                uikitModal:null,
                newPackage: false,
                output: '',
                status: 'success',
                finished:false,
                modal:{
                    button:{
                        accept:{
                            text: 'Install',
                            isDisabled:false,
                            isShow:true
                        },
                        cancel:{
                            text: 'Cancel',
                            isDisabled:false,
                            isShow:true
                        }
                    },
                    isLoader:false,
                    progressbar:0
                }
            }
        },

        mixins:[
            require('../../../../app/lib/client')
        ],

        watch:{
            'modal.isLoader':{
                handler(newValue){
                    if(newValue){
                        this.modal.button.cancel.isDisabled = newValue;
                        this.modal.button.accept.isDisabled = newValue;
                    }
                },
                deep:true
            }
        },

        created(){
            if(this.system.isClientSuitable && !this.system.isDeveloper){
                this.getVersion();
            }
        },

        mounted(){
            this.uikitModal = UIkit.modal('#versionStableModal');
        },

        methods:{
            openModal(){
                this.uikitModal.show();
            },

            getVersion(){
                this.clientResource('api/client/versions/get' , {constraint:this.system.version.constraint}).then((res)=>{
                    let data = res.data.version;
                    if(Version.compare(this.system.version.version , data.version , '<=')){
                        this.hasUpdate = res.data.version;
                        return;
                    }
                    this.hasUpdate = false;
                    return;
                }).catch((err)=>{
                    //Geliştirilecek
                })
            },

            tryGetAccess(){
                this.getAccessToken();
                let ref = this;
                setTimeout(function(){
                    ref.getVersion();
                } , 2000)
            },

            doDownload(){
                this.output = ``;
                this.output += `Server Side Connecting..\n`;
                setTimeout(() => {
                    this.output += `<span class="uk-label uk-text-capitalize">Your Client</span> ${this.system.config.oauth2.client}\n`;
                    this.output += `<span class="uk-label uk-text-capitalize">Server Side</span> ${this.client.system_api}...\n\n`;
                    this.modal.progressbar = 10
                }, 3000);
                setTimeout(() => {
                    this.output += `<span class="uk-text-success">Server Side Connect</span>\n`;
                    this.output += `<span class="uk-text-warning">Secret Client</span> ${this.system.config.oauth2.secret_client}\n`;
                    this.output += `<span class="uk-text-warning">Secret Key</span> ${this.system.config.oauth2.secret_key}\n`;
                    this.output += `<span class="uk-text-success">Get Access Token</span>...\n\n`;
                    this.modal.progressbar = 20
                }, 5000);
                setTimeout(() => {
                    this.output += `<span class="uk-text-success">Access Token:</span> ${this.client.access_token}...\n\n`;
                    this.modal.progressbar = 30
                }, 7000);
                setTimeout(()=>{
                    this.output += `<span class="uk-text-danger">A secure connection with <strong>Access Token</strong> is complete and deleted.</span>\n`;
                    this.output += `New version downloading\n`;
                    this.modal.progressbar = 40
                } , 9000)
                let ref = this;
                this.modal.isLoader = true;
                this.modal.progressbar = 1
                this.$http.get('admin/system/update/api/download-release' , {params:{
                    constraint:this.system.version.constraint
                }} , {
                    progress(e) {
                        if (e.lengthComputable) {
                            ref.modal.progressbar = (e.loaded / e.total ) * 50;
                        }
                    }
                }).then((res)=>{
                    this.output += `Finish Download...\n\n`;
                    this.modal.progressbar = 78;
                    this.doInstall();
                }).catch((err)=>{
                    this.$notify(err.bodtText , 'danger')
                    this.closeModal();
                    return;
                })
            },

            doInstall(){
                this.output += `Starting Update...\n\n`;
                this.modal.progressbar = 79;
                this.modal.isLoader = true;
                this.$http.get('admin/system/update/update', null).then((res)=>{
                    this.setOutput(res.bodyText);
                    this.doMigration();
                }).catch((err)=>{
                    this.error();
                })
              
            },

            doMigration() {
                this.modal.progressbar = 100;
                if (this.status === 'success') {
                    this.$http.get('admin/system/migration/migrate').then(function (res) {
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

            closeModal(){
                this.modal.button.cancel.isDisabled = true;
                this.modal.button.accept.isDisabled = true;
                this.modal.isLoader = true;
                const ref = this;
                setTimeout(function(){
                    ref.modal.button.cancel.isDisabled = false;
                    ref.modal.button.accept.isDisabled = false;
                    ref.modal.isLoader = false;
                    ref.modal.progressbar = 0;
                    ref.uikitModal.hide();
                } , 2000)
            },

            setOutput(output) {
                const lines = output.split('\n');
                const match = lines[lines.length - 1].match(/^status=(success|error)$/);
                if (match) {
                    this.status = match[1];
                    delete lines[lines.length - 1];
                    this.output += lines.join('\n');
                } else {
                    this.output += output;
                }
            },

            error(error) {
                this.$notify(this.$trans('Whoops, something went wrong.'));
                this.deletePackage();
            },

            abort(msg){
                this.modal.isLoader = false
                this.$notify(msg , 'danger');
                this.modal.progressbar = 0;
            }
        }
    }
</script>

<style>
    .update-hover:hover > * {
        color:#fff;
        opacity: 1;
    }
    .version-dev{
        background: #fb256a !important;
        color: white !important;
        font-size: 10px;
        padding: 5px 10px;
        margin-left: 10px;
        font-weight: bold;
        text-transform: capitalize;
    }
    .tm-text-small{
        font-size:11px;
    }
    .update{
        position:relative;
    }
    .hasupdateanimation{
        display: -webkit-inline-box;
        position: absolute;
        top: -2px;
        width: 17px;
        left: -20px;
        height: 17px;
        border-radius: 100px;
        -webkit-animation: alert 0.8s infinite; /* Safari 4+ */
        -moz-animation:    alert 0.8s infinite; /* Fx 5+ */
        -o-animation:      alert 0.8s infinite; /* Opera 12+ */
        animation:         alert 0.8s infinite; /* IE 10+, Fx 29+ */
    }

    @keyframes alert {
        0%   { background: #22af49; }
        50%   { background: #5aea93; }
        100% { background: #22af49; }
    }
</style>