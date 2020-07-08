export default function (Vue) {
    const _ = Vue.util;
    const cache = {};

    /**
     * Asset provides a promise based assets manager.
     */
    function Asset(assets) {
        const promises = []; const $url = (this.$url || Vue.url); let
            _assets = [];

        Object.keys(assets).forEach((type) => {
            if (!Asset[type]) {
                return;
            }

            // _assets = _.isArray(assets[type]) ? assets[type] : [assets[type]];
            _assets = Array.isArray(assets[type]) ? assets[type] : [assets[type]];

            for (let i = 0; i < _assets.length; i++) {
                if (!_assets[i]) {
                    continue;
                }

                if (!cache[_assets[i]]) {
                    cache[_assets[i]] = Asset[type]($url(_assets[i]));
                }

                promises.push(cache[_assets[i]]);
            }
        });

        return Vue.Promise.all(promises).bind(this);
    }

    _.extend(Asset, {

        css(url) {
            return new Vue.Promise(((resolve, reject) => {
                const link = document.createElement('link');

                link.onload = function () {
                    resolve(url);
                };
                link.onerror = function () {
                    reject(url);
                };

                link.href = url;
                link.type = 'text/css';
                link.rel = 'stylesheet';

                document.getElementsByTagName('head')[0].appendChild(link);
            }));
        },

        js(url) {
            return new Vue.Promise(((resolve, reject) => {
                const script = document.createElement('script');

                script.onload = function () {
                    resolve(url);
                };
                script.onerror = function () {
                    reject(url);
                };
                script.src = url;

                document.getElementsByTagName('head')[0].appendChild(script);
            }));
        },

        image(url) {
            return new Vue.Promise(((resolve, reject) => {
                const img = new Image();

                img.onload = function () {
                    resolve(url);
                };
                img.onerror = function () {
                    reject(url);
                };

                img.src = url;
            }));
        },

    });

    Object.defineProperty(Vue.prototype, '$asset', {

        get() {
            return _.extend(Asset.bind(this), Asset);
        },

    });

    Vue.asset = Asset;

    return Asset;
}
