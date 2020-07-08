import settings from '../components/widget-settings.vue';
import visibility from '../components/widget-visibility.vue';
import TemplateSettings from '../components/template-settings';
import { ValidationObserver, VInput} from '../../../../app/components/validation.vue';

var WidgetEdit = {

    name: 'widget',

    el: '#widget-edit',

    mixins: [window.Widgets, Theme.Mixins.Helper],

    provide: {
        '$components': {
            'template-settings': TemplateSettings,
            'v-input' : VInput
        }
    },

    data() {
        return _.merge({
            form: {}, sections: [], active: 0, processing: false,
        }, window.$data);
    },

    created() {
        let sections = []; const type = _.kebabCase(this.widget.type); let
            active;

        _.forIn(this.$options.components, (component, name) => {
            if (component.section) {
                sections.push(_.extend({ name, priority: 0 }, component.section));
            }
        });

        sections = _.sortBy(sections.filter((section) => {
            // active = section.name.match('(.+)--(.+)');
            let name = section.name;
            active = (name.match(/\.[^.]/) && !name.match(/\s/)) ? name.match(/(.*(?=\.))\.(.*)/) : null

            if (active === null) {
                return !_.find(sections, { name: `${type}.${section.name}` });
            }

            return active[1] == type;
        }, this), 'priority');

        this.$set(this, 'sections', sections);
    },

    mounted() {
        this.tab = UIkit.tab('#widget-tab', { connect: '#widget-content' });

        const vm = this;

        UIkit.util.on(this.tab.connects, 'show', (e, tab, sel) => {
            if (tab != vm.tab) return false;
            for (const index in tab.toggles) {
                if (tab.toggles[index].parentNode.classList.contains('uk-active')) {
                    vm.active = index;
                    break;
                }
            }
        });

        this.$watch('active', function (active) {
            this.tab.show(active);
        });

        this.$state('active');

        // set position from get param
        if (!this.widget.id) {
            const match = new RegExp('[?&]position=([^&]*)').exec(location.search);
            this.widget.position = (match && decodeURIComponent(match[1].replace(/\+/g, ' '))) || '';
        }
    },

    methods: {

        async submit() {
            const isValid = await this.$refs.observer.validate();
            if (isValid) {
                this.processing = true;
                this.save();
            }
        },

        save() {
            const vm = this;

            this.$trigger('save:widget', { widget: this.widget });

            this.$resource('api/site/widget{/id}').save({ id: this.widget.id }, { widget: this.widget }).then(function (res) {
                const { data } = res;

                this.$trigger('saved:widget');

                if (!this.widget.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/site/widget/edit', { id: data.widget.id }));
                }

                this.$set(this, 'widget', data.widget);

                this.$notify('Widget saved.');
                setTimeout(() => {
                    vm.processing = false;
                }, 500);
            }, function (res) {
                this.$notify(res.data, 'danger');
            });
        },

        cancel() {
            // TODO
            this.$trigger('cancel:widget');
        },

    },

    components: {
        settings,
        visibility,
        ValidationObserver
    }

};

export default WidgetEdit;

Vue.ready(WidgetEdit);
