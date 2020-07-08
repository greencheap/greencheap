const _ = Vue.util;

export default {

    bind(el, binding, vnode) {
        binding.def.update(el, binding, vnode);
    },

    update(el, binding, vnode) {
        const buttons = (el.getAttribute('buttons') || '').split(',');

        let options = {
            title: false,
            labels: {
                ok: buttons[0] || vnode.context.$trans('Ok'),
                cancel: buttons[1] || vnode.context.$trans('Cancel'),
            },
            stack: true,
        };

        // // vue-confirm="'Title':'Text...?'"
        // if (this.arg) {
        //     this.options.title = this.arg;
        // }

        // vue-confirm="'Text...?'"
        if (typeof binding.value === 'string') {
            options.text = binding.value;
        }

        // vue-confirm="{title:'Title', text:'Text...?'}"
        if (typeof binding.value === 'object') {
            options = _.extend(options, binding.value);
        }

        const handler = vnode.data.on.click.fns;

        vnode.data.on.click.fns = function (e) {
            const modal = UIkit.modal.confirm(vnode.context.$trans(binding.value), options).then(() => {
                handler(e);
            }, () => {});
        };
    },

    unbind(el, binding, vnode) {},

};
