<template>
    <div>
        <div v-if="!system.isClientSuitable && system.isDeveloper">
            <a @click.prevent="openModal" class="tm-text-small update-hover">
                <i class="tm-icon-update uk-margin-small-right"></i>
                <span>{{'Upload Package' | trans}}</span>
            </a>
        </div>
        <div class="versionModal uk-flex-top" uk-modal="bg-close:false;esc-close:false">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-header">
                    <h2 class="uk-modal-title">{{modal.title}}</h2>
                    <hr>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-placeholder uk-height-small uk-flex uk-flex-middle uk-flex-center" v-if="!system.isClientSuitable && !newPackage">
                        <div>
                            <span uk-icon="icon: cloud-upload"></span>
                            <span class="uk-text-middle">{{'Attach binaries by dropping them here or' | trans}}</span>
                            <div uk-form-custom>
                                <input @change.prevent="onChangeUpload" type="file">
                                <span class="uk-link">{{'selecting one' | trans}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div v-if="newPackage && !output">
                            <div class="uk-height-medium" uk-overflow-auto>
                                <div class="uk-background uk-background-muted uk-padding-small">
                                    <div>
                                        <div v-html="$options.filters.markdown(newPackage.readme)"></div>
                                        <hr>
                                        <h1>{{'Changelog' | trans}}</h1>
                                        <div class="uk-grid-small" uk-grid v-html="changelog(newPackage.changelog)"></div>
                                    </div>
                                </div>
                            </div>

                            <ul class="uk-list uk-list-divider">
                                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                    <span class="uk-margin-small-right">{{'YOUR VERSION' | trans}}</span> <strong>{{system.version.full}}</strong>
                                </li>
                                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                    <span class="uk-margin-small-right">{{'NEW VERSION' | trans}}</span> <strong>{{newPackage.version.full}}</strong>
                                </li>
                                <li class="uk-text-capitalize uk-text-small" style="letter-spacing: 2px;">
                                    <span class="uk-margin-small-right">{{'CONSTRAINT' | trans}}</span> <strong class="uk-text-primary uk-text-lowercase">{{newPackage.version.constraint}}</strong>
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
                    <a :class="'uk-button uk-button-primary'" :href="$url.route('admin/dashboard')" v-show="finished">{{ 'New Version Installed' | trans }}</a>
                    <button @click.prevent="deletePackage" class="uk-button uk-button-default" type="button" :disabled="modal.button.cancel.isDisabled" v-show="modal.button.cancel.isShow || !finished">{{modal.button.cancel.text | trans}}</button>
                    <button @click.prevent="doInstall" class="uk-button uk-button-primary" type="button" :disabled="modal.button.accept.isDisabled" v-show="modal.button.accept.isShow || !finished">{{modal.button.accept.text | trans}}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        section:{
            label:'Update Dev',
            priority:-500
        },

        data(){
            return {
                uikitModal:null,
                newPackage: false,
                output: '',
                status: 'success',
                finished:false,
                modal:{
                    title: 'Upload Package',
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

        mounted(){
            this.uikitModal = UIkit.modal('.versionModal');
        },

        methods:{
            openModal(){
                this.uikitModal.show();
            },

            onChangeUpload(e){
                this.modal.progressbar = 10;
                this.modal.isLoader = true;

                const formData = new FormData();
                const file = e.target.files[0];
                
                if( file.type != 'application/zip' ){
                    this.abort(this.$trans('Uploaded file extension is invalid. Only ZIP extensions are allowed.'));
                }

                formData.append('_package' , file , file.name);
                formData.set('_version' , this.system.version.full);
                this.modal.progressbar = 50;
                this.$http.post('admin/system/update/api/upload-package' , formData).then((res)=>{
                    this.modal.progressbar = 70;
                    const data = res.body;
                    this.newPackage = data;
                    this.modal.isLoader = false;
                    this.modal.button.cancel.isDisabled = false;
                    this.modal.button.accept.isDisabled = false;
                }).catch((err)=>{
                    this.abort(err.bodyText)
                })
            },

            doInstall(){
                this.output += `Start Update...\n\n`;
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

            deletePackage(){
                this.modal.button.cancel.isDisabled = true;
                this.modal.button.accept.isDisabled = true;
                this.modal.isLoader = true;
                if(this.modal.progressbar > 30){
                    this.modal.progressbar = 30;
                }
                if(this.newPackage){
                    this.$http.get('admin/system/update/api/remove-package' , {params:{fullPath:this.newPackage.fullPath}}).then((res)=>{
                        this.newPackage = false;
                    })
                }
                const ref = this;
                setTimeout(function(){
                    ref.modal.button.cancel.isDisabled = false;
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
                    this.output = lines.join('\n');
                } else {
                    this.output = output;
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
</style>