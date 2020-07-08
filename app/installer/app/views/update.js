var Update = {
    name: 'UpdateSystem',
    el: '#app',

    mixins: [
        require('../../../system/app/lib/client'),
    ],

    data(){
        return {
            output: '',
            processing:false,
            progress: 0,
            version:false,
            hasVersion:false
        }
    },

    watch:{
        'access_token':{
            handler(value){
                if(value){
                    this.getUpdate()
                }
            },
            deep:true
        }
    },

    methods:{
        install() {
            this.processing = true;
            this.doDownload(this.version);
        },

        doDownload(version) {
            this.$set(this, 'progress', 33);
            this.$http.post('admin/system/update/download', { constraint: version.data.constraint }).then(this.doInstall, this.error);
        },

        doInstall(){
            console.log('Kıhs')
        },

        getUpdate(){
            this.clientResource('api/client/versions' , {access_token:this.access_token , scoped:1 , constraint:this.system.constraint}).then((res)=>{
                let data = res.body.last_version;
                this.version = data;
                if(Version.compare(this.system.version , data.data.version , '<')){
                    this.hasVersion = true;
                    return;
                }
                return this.hasVersion = false;
            }).catch((err)=>{
                this.$notify(this.$trans('Failed to withdraw from version client. Contact Us') , 'danger');
            })
        },

        changelog(md) {
            const renderer = new marked.Renderer();
            let section;

            renderer.heading = function (text) {
                section = text;
                return '';
            };

            renderer.listitem = function (text) {
                switch (section) {
                case 'Added':
                    return `<div class="uk-width-1-6"><span class="uk-label uk-label-primary uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                case 'Deprecated':
                    return `<div class="uk-width-1-6"><span class="uk-label uk-label-warning uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                case 'Removed':
                    return `<div class="uk-width-1-6"><span class="uk-label uk-label-warning uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                case 'Fixed':
                    return `<div class="uk-width-1-6"><span class="uk-label uk-label-danger uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                case 'Security':
                    return `<div class="uk-width-1-6"><span class="uk-label uk-label-danger uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                default:
                    return `<div class="uk-width-1-6"><span class="uk-label uk-text-capitalize uk-text-center uk-width-expand">${section}</span></div><div class="uk-width-5-6">${text}</div>`;
                }
            };

            renderer.list = function (text) {
                return text;
            };

            return marked(md, { renderer });
        },
    }
};

import Version from '../lib/version';
export default Update;

Vue.ready(Update);