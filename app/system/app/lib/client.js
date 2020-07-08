module.exports = {

    data(){
        return _.merge({
            errorTemplate:{
                isActive:false,
                message:'',
                title:''
            }
        } , window.$client)
    },

    computed:{
        isError(){
            if( this.errorTemplate.isActive ){
                return true;
            }
            return false;
        },

        getTitle(){
            return this.errorTemplate.title;
        },

        getMessage(){
            return this.errorTemplate.message;
        }
    },

    methods:{
        getAccessToken(){
            this.clientResource('api/oauth2/resource-owner/client' , {
                secret_client:this.system.config.oauth2.secret_client,
                secret_key:this.system.config.oauth2.secret_key,
            }).then((res)=>{
                this.client.access_token = res.data.access_token;
                if(this.isError){
                    this.abort(false);
                }
                return res;
            }).catch((err)=>{
                this.$notify(err.bodyText , 'danger');
                console.log(err)
                this.abort(true  , err.bodyText);
                return err;
            })
        },

        clientResource(url , parameter){
            let urlParam = `${this.client.system_api}/${url}`;

            if(!parameter){
                parameter = {}
            }
            
            if(this.client.access_token){
                parameter = _.merge({access_token:this.client.access_token} , parameter)
            }else{
                parameter = parameter
            }

            let http = this.$http.get(urlParam , 
                {
                    params:parameter
                }
            )
            return http;
        },

        abort(active = false, message = '', title = 'Error'){
            this.errorTemplate.isActive = active;
            this.errorTemplate.message  = message;
            this.errorTemplate.title  = title;
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
        }
    }
}