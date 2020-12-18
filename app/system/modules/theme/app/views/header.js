const header = {
    el: "#header",
    name: "ThemeHeader",
    data() {
        return _.merge({
            darkMode: this.$session.get('darkMode', 0)
        }, window.$greencheap)
    },

    watch: {
        darkMode: {
            handler(status) {
                this.$session.set('darkMode', status)
                const htmlElement = document.getElementsByTagName('html')[0];
                if (status) {
                    htmlElement.classList.add('uk-section-secondary', 'tm-darkmode', 'uk-light');
                    return;
                }
                htmlElement.classList.remove('uk-section-secondary', 'tm-darkmode', 'uk-light');
                return;
            },
            immediate: true
        }
    },

    methods: {
        onDarkMode() {
            this.darkMode = this.$session.get('darkMode') ? 0 : 1;
        }
    }
};

export default header;
Vue.ready(header);