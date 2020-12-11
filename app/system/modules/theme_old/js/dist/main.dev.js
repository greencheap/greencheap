"use strict";

var _uikit = _interopRequireDefault(require("uikit"));

var _uikitUtil = require("uikit-util");

var _autocomplete = _interopRequireDefault(require("./components/autocomplete"));

var _pagination = _interopRequireDefault(require("./components/pagination"));

var _htmleditor = _interopRequireDefault(require("./components/htmleditor"));

var _notifications = _interopRequireDefault(require("../../notifications/app/components/notifications.vue"));

var _update = _interopRequireDefault(require("./components/update.vue"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

/* eslint-disable no-unused-vars */
var sidebar = {
  el: '#sidebar',
  name: 'Sidebar',
  data: function data() {
    return _.merge({
      navs: null
    }, window.$greencheap);
  },
  created: function created() {
    var menu = _(this.menu).groupBy('layout').value();

    var item = _(menu.sidebar).sortBy('priority').groupBy('parent').value();

    this.$set(this, 'navs', item.root);
  }
};
var navbar = {
  el: '#navbar',
  name: 'Navbar',
  data: function data() {
    return _.merge({
      navs: null,
      item: null,
      subnav: null,
      title: null
    }, window.$greencheap);
  },
  created: function created() {
    var menu = _(this.menu).groupBy('layout').value();

    var item = _(menu.navbar).sortBy('priority').groupBy('parent').value();

    if (item.root) {
      this.$set(this, 'navs', item.root);
    }

    var allMenu = _(this.menu).sortBy('priority').groupBy('parent').value();

    var findActive = _.find(allMenu.root, 'active');

    this.title = findActive.label;

    var submenus = _(this.menu).groupBy('parent').value();

    if (submenus) {
      this.subnav = _(submenus[findActive.id]).sortBy('priority').value();
    }
  },
  components: {
    Notifications: _notifications["default"],
    Update: _update["default"]
  }
};
var mobile = {
  el: '#mobile',
  name: 'Mobile',
  data: function data() {
    return _.merge({
      navs: null
    }, window.$greencheap);
  },
  created: function created() {
    var menu = _(this.menu).groupBy('layout').value();

    var item = _(menu.sidebar).sortBy('priority').groupBy('parent').value();

    this.$set(this, 'navs', item.root);
  }
};
Vue.ready(sidebar);
Vue.ready(navbar);
Vue.ready(mobile);