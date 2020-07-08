var Info = {

    name: 'info',

    el: '#info',

    data: {
        info: window.$info,
    },

    computed: {
        VueVersion() {
            return window.Vue ? Vue.version : '-';
        },
        UIkitVersion() {
            return window.UIkit ? UIkit.version : '-';
        },
    },

};

export default Info;

Vue.ready(Info);
