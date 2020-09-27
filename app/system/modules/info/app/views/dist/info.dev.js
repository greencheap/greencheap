"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var Info = {
  name: 'info',
  el: '#info',
  data: {
    info: window.$info
  },
  computed: {
    VueVersion: function VueVersion() {
      return window.Vue ? Vue.version : '-';
    },
    UIkitVersion: function UIkitVersion() {
      return window.UIkit ? UIkit.version : '-';
    }
  }
};
var _default = Info;
exports["default"] = _default;
Vue.ready(Info);