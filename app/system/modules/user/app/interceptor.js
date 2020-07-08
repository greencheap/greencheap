import modal from './components/modal-login.vue';

let mutex;

Vue.http.interceptors.push((request) => {
    let options;

    options = _.clone(request);

    return function (response) {
        if (options.crossOrigin || response.status !== 401 || options.headers.get('X-LOGIN')) {
            return response;
        }

        if (!mutex) {
            mutex = new Vue(modal).promise.finally(() => {
                mutex = undefined;
            });
        }

        return mutex.then(() => Vue.http(options));
    };
});
