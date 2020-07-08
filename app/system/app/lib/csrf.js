export default function (Vue) {
    Vue.http.interceptors.unshift((request) => {
        if (!request.crossOrigin) {
            request.headers.set('X-XSRF-TOKEN', Vue.cache.get('_csrf'));
        }
    });

    Vue.cache.set('_csrf', window.$greencheap.csrf);
}
