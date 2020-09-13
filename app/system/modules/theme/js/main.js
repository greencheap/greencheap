/* eslint-disable no-unused-vars */
import UIkit from 'uikit';
import {
    $, on, css, toNodes, isString, assign, html, remove,
// eslint-disable-next-line import/no-unresolved
} from 'uikit-util';
import Autocomplete from './components/autocomplete';
import Pagination from './components/pagination';
import HTMLEditor from './components/htmleditor';
import Notifications from '../../notifications/app/components/notifications.vue';
import Update from './components/update.vue';

const sidebar = {
    el: '#sidebar',
    name: 'Sidebar',
    data() {
        return _.merge({
            navs: null,
        }, window.$greencheap);
    },

    created() {
        const menu = _(this.menu).groupBy('layout').value();
        const item = _(menu.sidebar).sortBy('priority').groupBy('parent').value();
        this.$set(this, 'navs', item.root);
    },
};

const navbar = {
    el: '#navbar',
    name: 'Navbar',
    data() {
        return _.merge({
            navs: null,
            item: null,
            subnav: null,
            title: null,
        }, window.$greencheap);
    },

    created() {
        const menu = _(this.menu).groupBy('layout').value();
        const item = _(menu.navbar).sortBy('priority').groupBy('parent').value();
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
    },

    components: {
        Notifications,
        Update,
    },
};

const mobile = {
    el: '#mobile',
    name: 'Mobile',
    data() {
        return _.merge({
            navs: null,
        }, window.$greencheap);
    },

    created() {
        const menu = _(this.menu).groupBy('layout').value();
        const item = _(menu.sidebar).sortBy('priority').groupBy('parent').value();
        this.$set(this, 'navs', item.root);
    }
}

Vue.ready(sidebar);
Vue.ready(navbar);
Vue.ready(mobile);
