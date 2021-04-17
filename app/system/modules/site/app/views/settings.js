import SiteCode from '../components/site-code.vue';
import SiteMeta from '../components/site-meta.vue';
import SiteGeneral from '../components/site-general.vue';
import SiteMaintenance from '../components/site-maintenance.vue';

import { ValidationObserver, VInput } from 'SystemApp/components/validation.vue';

window.Site = {

    name: 'site-settings',

    el: '#settings',

    mixins: [Theme.Mixins.Helper, Theme.Mixins.Elements],

    provide: {
        '$components': {
            'v-input': VInput
        }
    },

    data() {
        return _.merge({ form: {} }, window.$data);
    },

    created() {
        this.$theme.$tabs('leftTab', '#settings .uk-nav', { connect: '.settings-tab', state: true });
    },

    computed: {

        sections() {
            const sections = [];
            const hash = window.location.hash.replace('#', '');

            _.forIn(this.$options.components, (component, name) => {
                const { section } = component;

                if (component.section) {
                    section.name = name;
                    section.active = name == hash;
                    sections.push(section);
                }
            });

            return sections;
        },

    },

    methods: {

        async submit() {
            const isValid = await this.$refs.observer.validate();
            if (isValid) {
                this.save();
            }
        },

        save() {
            this.$trigger('save:settings', this.config);

            this.$http.post('admin/system/settings/config', { name: 'system/site', config: this.config }).then(function() {
                this.$notify('Settings saved.');
            }, function(res) {
                this.$notify(res.data, 'danger');
            });
        },

    },

    components: {
        'site-code': SiteCode,
        'site-meta': SiteMeta,
        'site-general': SiteGeneral,
        'site-maintenance': SiteMaintenance,
        'validation-observer': ValidationObserver
    },

};

Vue.ready(window.Site);