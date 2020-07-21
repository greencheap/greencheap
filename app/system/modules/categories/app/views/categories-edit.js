/* eslint-disable no-restricted-syntax */
import { ValidationObserver, VInput } from 'SystemApp/components/validation.vue';
import Section from "../components/categories-content.vue";

const categories = {
    el: '#app',
    name: 'Categories',
    data() {
        return _.merge({
            sections: [],
            form: {},
            active: 0,
            processing: false,
        }, window.$data);
    },

    created() {
        let sections = []; const type = _.kebabCase(this.type.id); let active;
        _.forIn(this.$options.components, (component, name) => {
            if (component.section) {
                sections.push(_.extend({ name, priority: 0 }, component.section));
            }
        });

        sections = _.sortBy(sections.filter((section) => {
            const { name } = section;
            active = (name.match(/\.[^.]/) && !name.match(/\s/)) ? name.match(/(.*(?=\.))\.(.*)/) : null;

            if (active === null) {
                return !_.find(sections, { name: `${type}.${section.name}` });
            }

            return active[1] === type;
        }), 'priority');

        this.$set(this, 'sections', sections);
    },

    mounted() {
        const vm = this;

        this.Nodes = this.$resource('api/site/node{/id}');

        this.tab = UIkit.tab('#tab', { connect: '#content' });

        // eslint-disable-next-line consistent-return
        UIkit.util.on(this.tab.connects, 'show', (e, tab) => {
            if (tab !== vm.tab) return false;
            for (const index in tab.toggles) {
                if (tab.toggles[index].parentNode.classList.contains('uk-active')) {
                    vm.active = index;
                    break;
                }
            }
        });

        this.$watch('active', (active) => {
            this.tab.show(active);
        });

        this.$state('active');
    },

    components: {
        Section,
    },
};

export default categories;

window.Categories = categories;

Vue.ready(window.Categories);
