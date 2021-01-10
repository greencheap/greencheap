var Update = {
    el: '#app',
    name: 'UpdateSystem',

    data() {
        return _.merge({
            hasUpdate: false
        }, window.$data)
    }
};

Vue.ready(Update);