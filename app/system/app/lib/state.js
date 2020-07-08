export default function (Vue) {
    const State = function (key, value) {
        const vm = this;

        const current = (new RegExp(`${key}=([^&]*)&?`)).exec(location.search);

        if (!value && current) {
            vm.$set(vm, key, current[1]);
        }

        if (value !== undefined) {
            history.replaceState({ key, value: this[key] }, '', modifyQuery(location.search, key, value));
        }

        this.$watch(key, (value) => {
            history.pushState({ key, value }, '', modifyQuery(location.search, key, value));
        });

        window.onpopstate = function (event) {
            if (event.state && event.state.key === key) {
                vm.$set(vm, key, event.state.value);
            }
        };
    };

    Object.defineProperty(Vue.prototype, '$state', {

        get() {
            return State.bind(this);
        },

    });
}

function modifyQuery(query, key, value) {
    query = query.substr(1);
    query = query.replace(new RegExp(`${key}=[^&]*&?`), '');

    if (query.length && query[query.length - 1] !== '&') {
        query += '&';
    }

    return `?${query}${[key, value].join('=')}`;
}
