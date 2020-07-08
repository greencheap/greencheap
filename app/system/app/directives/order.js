import { $, on, append, addClass, removeClass, remove, find } from 'uikit-util';

export default {

    bind(el, binding, vnode) {
        binding.dir = '';
        binding.active = false;
        binding.indicator = $('<i class="uk-margin-small-left"></i>');

        addClass(el, 'pk-table-order uk-visible-toggle');
        el._off = on(el, 'click', () => {
            binding.dir = (binding.dir == 'asc') ? 'desc' : 'asc';
            _.set(vnode.context, binding.expression, [binding.arg, binding.dir].join(' '));
        });
        append(el, binding.indicator);
    },

    update(el, binding, vnode) {
        const data = binding.value;

        const parts = data.split(' ');
        const field = parts[0];
        const dir = parts[1] || 'asc';

        binding.indicator = find('i', el);

        removeClass(binding.indicator, 'pk-icon-arrow-up pk-icon-arrow-down');
        removeClass(el, 'uk-active');

        if (field == binding.arg) {
            binding.active = true;
            binding.dir = dir;

            addClass(el, 'uk-active');
            removeClass(binding.indicator, 'uk-invisible-hover');
            addClass(binding.indicator, dir == 'asc' ? 'pk-icon-arrow-down' : 'pk-icon-arrow-up');
        } else {
            addClass(binding.indicator, 'pk-icon-arrow-down uk-invisible-hover');
            binding.active = false;
            binding.dir = '';
        }
    },

    unbind(el, binding, vnode) {
        removeClass(el, 'pk-table-order');
        el._off();
        remove(binding.indicator);
    }

};
