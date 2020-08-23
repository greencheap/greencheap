"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _version = _interopRequireDefault(require("../lib/version"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var marketplace = {
  el: '#marketplace',
  name: 'Marketplace',
  data: function data() {
    return _.merge({
      pkgs: false,
      config: {
        filter: this.$session.get('pkgs-front.filter', {
          order: 'download_count desc',
          type: 'greencheap-extension'
        })
      },
      pages: 0,
      count: '',
      modalpkg: false,
      client: window.$client,
      output: '',
      isLoader: true,
      status: ''
    }, window.$data);
  },
  mixins: [// eslint-disable-next-line global-require
  require('../../../system/app/lib/client')],
  mounted: function mounted() {
    this.$watch('config.page', this.load, {
      immediate: true
    });
  },
  watch: {
    'config.filter': {
      handler: function handler(filter) {
        if (this.config.page) {
          this.config.page = 0;
        } else {
          this.load();
        }

        this.$session.set('pkgs-front.filter', filter);
      },
      deep: true
    },
    pkgs: {
      handler: function handler() {
        var _this = this;

        _.forEach(this.pkgs, function (pkg, key) {
          var controll = _.find(_this.installedPackages, function (installedPkg) {
            if (installedPkg.name === pkg.package_name) {
              return true;
            }

            return false;
          });

          _this.pkgs[key] = _.merge({
            installed: controll ? {
              name: controll.name,
              version: controll.version,
              type: controll.type,
              update: _this.checkVersion(pkg.version, controll.version, '>')
            } : false
          }, _this.pkgs[key]);
        });
      },
      deep: true
    }
  },
  methods: {
    load: function load() {
      var _this2 = this;

      this.clientResource('marketplace/getfilters', {
        filter: this.config.filter,
        page: this.config.page
      }).then(function (res) {
        var data = res.data;

        _this2.$set(_this2, 'pkgs', data.packages);

        _this2.$set(_this2, 'pages', data.pages);

        _this2.$set(_this2, 'count', data.count);
      })["catch"](function (err) {
        _this2.$notify(err.body.error, 'danger');
      });
    },
    setType: function setType(type) {
      this.config.filter.type = type;
    },
    checkVersion: function checkVersion(version, version2, operator) {
      return _version["default"].compare(version, version2, operator);
    },
    openModal: function openModal(pkg) {
      this.$set(this, 'modalpkg', pkg);
      this.$refs.modalDeatil.open();
    },
    getImage: function getImage(data) {
      return data.image.src;
    },
    downloadPackage: function downloadPackage(e) {
      var _this3 = this;

      e.target.innerHTML = "<span class=\"uk-margin-right\" uk-spinner></span>".concat(e.target.text);
      this.$http.get('admin/system/package/downloadpackage', {
        params: {
          id: this.modalpkg.id,
          type: this.modalpkg.type
        }
      }).then(function (res) {
        var pkg = res.data["package"];

        _this3.doInstall(pkg);
      })["catch"](function (err) {
        _this3.$notify(err.bodyText, 'danger');
      });
    },
    doInstall: function doInstall(pkg, packages, onClose, packagist) {
      var _this4 = this;

      var self = this;
      return this.$http.post('admin/system/package/install', {
        "package": pkg,
        packagist: Boolean(packagist)
      }, {
        progress: function progress(e) {
          if (e.lengthComputable) {
            self.$refs.modalDeatil.close();
            self.$refs.installDetail.open();
            self.output += 'Starting\n\n';
          }
        }
      }).then(function (res) {
        var patt = new RegExp('^status=(.+)', 'gm');

        _this4.setOutput(res.bodyText);

        var getStatusTest = patt.exec(res.bodyText);
        _this4.status = getStatusTest[1];
        _this4.isLoader = false;
      })["catch"](function (err) {
        _this4.$notify(err.data, 'danger');

        _this4.close();
      });
    },
    setOutput: function setOutput(output) {
      var lines = output.split('\n');
      var match = lines[lines.length - 1].match(/^status=(success|error)$/);

      if (match) {
        // eslint-disable-next-line prefer-destructuring
        this.status = match[1];
        delete lines[lines.length - 1];
        this.output += lines.join('\n');
      } else {
        this.output += output;
      }
    },
    cancelPkg: function cancelPkg() {
      // eslint-disable-next-line no-restricted-globals
      this.modalpkg = false;
      location.reload();
    },
    enablePkg: function enablePkg() {
      var _this5 = this;

      return this.$http.post('admin/system/package/enable', {
        name: this.modalpkg.package_name
      }).then(function () {
        _this5.$notify(_this5.$trans('"%title%" enabled.', {
          title: _this5.modalpkg.title
        }));

        document.location.assign(_this5.$url("admin/system/package/".concat(_this5.modalpkg.type === 'greencheap-theme' ? 'themes' : 'extensions')));
      }, this.error);
    }
  }
};
var _default = marketplace;
exports["default"] = _default;
Vue.ready(marketplace);