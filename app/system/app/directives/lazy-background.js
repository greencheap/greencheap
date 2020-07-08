export default {

    update(el, binding, vnode) {
        const img = new Image();
        const { value } = binding;

        img.onload = function () {
            UIkit.util.css(el, 'background-image', `url('${value}')`);
        };

        img.src = value;
    },

};
