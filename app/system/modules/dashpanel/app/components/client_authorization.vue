<template>
    <div>
        <div>
            <a @click.prevent="openModal" class="tm-text-small update-hover">
                <i class="tm-icon-coffee uk-margin-small-right"></i>
                <span>{{'Client Authorization' | trans}}</span>
            </a>
        </div>

        <div class="clientAuthorization uk-flex-top" uk-modal="bg-close:false;esc-close:false">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-header">
                    <h2 class="uk-modal-title uk-margin-remove">{{'Client Authorization' | trans}}</h2>
                    <hr>
                </div>
                <div class="uk-modal-body uk-form-horizontal">

                    <div class="uk-margin">
                        <label class="uk-form-label">{{'Secret Client' | trans}}</label>
                        <div class="uk-form-controls">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: database"></span>
                                 <input class="uk-input uk-width-expand" type="text" v-model="system.config.oauth2.secret_client" :placeholder="'Your Secret Client' | trans">
                            </div>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">{{'Secret Key' | trans}}</label>
                        <div class="uk-form-controls">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: git-branch"></span>
                                 <input class="uk-input uk-width-expand" type="password" v-model="system.config.oauth2.secret_key" :placeholder="'Your Secret Key' | trans">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-footer uk-position-relative uk-text-right">
                    <hr>
                    <div class="uk-position-absolute uk-position-center-left uk-position-medium" style="bottom:-50px" v-show="modal.isLoader">
                        <i uk-spinner></i>
                        <span class="uk-margin-left">{{'Wait' | trans}}</span>
                    </div>
                    <button @click.prevent="closeModal" class="uk-button uk-button-default" type="button" :disabled="modal.button.cancel.isDisabled" v-show="modal.button.cancel.isShow || !finished">{{modal.button.cancel.text | trans}}</button>
                    <button @click.prevent="removeInformation" class="uk-button uk-button-secondary" type="button" :disabled="modal.button.accept.isDisabled" v-show="system.isDeveloper && system.isClientSuitable">{{'Remove Authorization' | trans}}</button>
                    <button @click.prevent="checkApi" class="uk-button uk-button-primary" type="button" :disabled="modal.button.accept.isDisabled" v-show="modal.button.accept.isShow || !finished">{{modal.button.accept.text | trans}}</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        section:{
            label:'Client Authorization',
            priority:1
        },

        data(){
            return {
                uikitModal:null,
                modal:{
                    button:{
                        accept:{
                            text: 'Check and Save',
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
                }
            } 
        },

        mixins:[
            require('../../../../app/lib/client')
        ],

        watch:{
            'modal.isLoader':{
                handler(val){
                    if(val){
                        this.modal.button.cancel.isDisabled = val;
                        this.modal.button.accept.isDisabled = val;
                    }
                },
                deep:true
            },

            'system.config.oauth2':{
                handler(val){
                    let secret_key = val.secret_key;
                    let secret_client = val.secret_client;
                    if(secret_key.length >= 100 && secret_client.length >= 50){
                        this.modal.button.accept.isDisabled = false;
                        return;
                    }
                    this.modal.button.accept.isDisabled = true;
                    return;
                },
                deep:true
            }
        },

        mounted(){
            this.uikitModal = UIkit.modal('.clientAuthorization');

            if(!this.system.isClientSuitable && !this.system.isDeveloper){
                this.modal.button.cancel.isDisabled = true;
                this.modal.button.accept.isDisabled = true;
                this.openModal();
            }
        },

        methods:{
            checkApi(){
                let ref = this;
                this.modal.isLoader = true;
                if(this.client.access_token){
                    this.client.access_token = false;
                }
                this.getAccessToken();

                setTimeout(function(){
                    if(ref.client.access_token){
                        ref.save()
                        return;
                    }
                    ref.$notify(ref.$trans('Try Again!') , 'danger')
                    ref.modal.isLoader = false;
                    ref.modal.button.accept.isDisabled = true;
                } , 2000)
            },

            save(){
                this.$http.post('admin/system/settings/config', {name: 'system', config: this.system.config})
                .then((err) => {
                    this.$notify(this.$trans('Saved'));
                    this.closeModal();
                    location.reload();
                })
                .catch((res) => {
                    this.$notify(res.data, 'danger');
                });
                this.modal.isLoader = false;
                this.modal.button.cancel.isDisabled = false;
                this.modal.button.accept.isDisabled = false;
            },

            removeInformation(){
                this.system.config.oauth2.secret_client = '';
                this.system.config.oauth2.secret_key = '';
                this.save();
            },

            openModal(){
                this.uikitModal.show();
            },

            closeModal(){
                this.modal.button.cancel.isDisabled = true;
                this.modal.button.accept.isDisabled = true;
                this.modal.isLoader = true;
                const ref = this;
                setTimeout(function(){
                    ref.modal.button.cancel.isDisabled = false;
                    ref.modal.isLoader = false;
                    ref.uikitModal.hide();
                } , 1000)
            },
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