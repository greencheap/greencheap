const categories = {
    el: '#app',
    name: 'Categories',
    data(){
        return _.merge({
            categories: false,
            config:{
                filters:this.$session.get('filters.config' , {order: 'date desc', limit:25, type: this.type})
            },
            pages: 0,
            count: '',
            selected: [],
            canEditAll:false
        } , window.$data)
    },

    mounted() {
        this.resource = this.$resource('admin/api/categories{/id}');
        this.$watch('config.page', this.load, {immediate: true});    
    },

    watch: {
        'config.filter': {
            handler(filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }

                this.$session.set('posts.filter', filter);
            },
            deep: true
        }
    },

    methods:{
        load(){
            this.resource.query({id:'get'} , {filters: this.config.filters, page:this.config.page}).then((res)=>{
                let {categories, pages, count} = res.data;
                this.$set(this , 'categories' , categories)
                this.$set(this , 'pages' , pages)
                this.$set(this , 'count' , count)
            })
        }
    }
}

Vue.ready(categories)
