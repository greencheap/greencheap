const header = {
    el: "#header",
    name: "ThemeHeader",
    data() {
        return _.merge({
            darkMode: this.$session.get('darkMode', 0),
            navs: null,
            subnav: null,
            title: null,
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

    created() {
        const item = _(this.menu).sortBy('priority').groupBy('parent').value();
        if (item.root) {
            this.$set(this, 'navs', item.root);
        }
        const allMenu = _(this.menu).sortBy('priority').groupBy('parent').value();
        const findActive = _.find(allMenu.root, 'active');
        this.title = findActive.label;
        const submenus = _(this.menu).groupBy('parent').value();
        if (submenus) {
            this.subnav = _(submenus[findActive.id]).sortBy('priority').value();
        }

        this.$notify('Hello World')
    },


    methods: {
        onDarkMode() {
            this.darkMode = this.$session.get('darkMode') ? 0 : 1;
        }
    },

    components: {
        Navbar
    }
};

import Navbar from '../components/navbar.vue';

export default header;
Vue.ready(header);