import util from 'uikit-util';

export default {

    params: ['group'],

    bind(el, binding, vnode) {
        binding.def.update(el, binding, vnode);
    },

    update(el, binding, vnode) {
        const group = binding.value.group ? `${binding.value.group} ` : '';
        const subSelector = binding.value.selector;
        const selector = group + binding.value.selector;

        binding.keypath = binding.rawName.indexOf(binding.arg) ? binding.rawName.slice(binding.rawName.indexOf(binding.arg)) : binding.arg;
        binding.selector = selector;
        binding.checked = false;
        binding.number = el.getAttribute('number') !== null;
        binding.selectors = [];

        binding.checkall = function (e) {
            e.stopImmediatePropagation();
            util.findAll(selector, vnode.context.$el).forEach((elem) => { elem.checked = e.target.checked; });
            binding.def.selected(true, el, binding, vnode);
        };

        binding.handler = [
            function (e) {
                e.stopImmediatePropagation();
                binding.def.selected(true, el, binding, vnode);
                binding.def.state(el, binding, vnode);
            },
            function (e) {
                e.stopImmediatePropagation();
                if (!(util.isInput(e.target) || e.target.tagName == 'A') && !window.getSelection().toString()) {
                    binding.selectors = Array.from(util.findAll(subSelector, this));

                    if (!binding.selectors.length) return;

                    if (binding.selectors.length == 1) {
                        binding.selectors[0].click();
                    } else {
                        for (let i = 1; i < binding.selectors.length; i++) {
                            if (binding.selectors[i].checked == binding.selectors[0].checked) {
                                binding.selectors[i].click();
                            }
                        }
                        binding.selectors[0].click();
                    }
                }
            },
        ];

        vnode.context.$nextTick(() => {
            util.on(el, 'change', binding.checkall);
            util.on(util.findAll(binding.selector, vnode.context.$el), 'change', binding.handler[0]); // this.$el
            util.on(util.findAll(`${group}.check-item`, vnode.context.$el), 'click', binding.handler[1]);
        });

        binding.unbindWatcher = vnode.context.$watch('selected', (selected) => {
            util.findAll(binding.selector, vnode.context.$el).forEach((elem) => {
                elem.checked = selected.indexOf(binding.def.toNumber(elem.value, el, binding, vnode)) !== -1;
            });

            binding.def.selected(undefined, el, binding, vnode);
            binding.def.state(el, binding, vnode);
        });
    },

    unbind(el, binding, vnode) {
        const group = binding.value.group ? `${binding.value.group} ` : '';

        util.off(el, 'change', binding.checkall);

        if (binding.handler) {
            util.findAll(binding.selector, vnode.context.$el).forEach((elem) => {
                util.off(elem, 'change', binding.handler[0]);
            });

            util.findAll(`${group}.check-item`, vnode.context.$el).forEach((elem) => {
                util.off(elem, 'click', binding.handler[1]);
            });
        }

        if (binding.unbindWatcher) {
            binding.unbindWatcher();
        }
    },

    state(el, binding, vnode) {
        if (binding.checked === undefined) {
            el.indeterminate = true;
        } else {
            el.checked = binding.checked;
            el.indeterminate = false;
        }
    },

    selected(update, el, binding, vnode) {
        const selected = []; const values = []; let
            value;

        util.findAll(binding.selector, vnode.context.$el).forEach((elem) => {
            value = binding.def.toNumber(elem.value, el, binding, vnode);
            values.push(value);

            if (elem.checked) {
                selected.push(value);
            }
        });

        if (update) {
            update = _.get(vnode.context, binding.keypath).filter(value => values.indexOf(value) === -1);
            _.set(vnode.context, binding.keypath, update.concat(selected));
        }

        if (selected.length === 0) {
            binding.checked = false;
        } else if (selected.length == values.length) {
            binding.checked = true;
        } else {
            binding.checked = undefined;
        }
    },

    toNumber(value, el, binding, vnode) {
        return binding.number ? Number(value) : value;
    },

};
