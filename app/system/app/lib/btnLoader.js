export default function (Vue) {
    Vue.prototype.$loader = (element, isLoader = true) => {
        const el = element.target;
        if (isLoader) {
            el.innerHTML = `<i class="uk-margin-small-right" uk-spinner="ratio:0.7"></i> ${el.innerText}`;
            el.setAttribute("disabled", true);
            return;
        }
        el.removeAttribute("disabled");
        if (el.hasChildNodes()) {
            el.removeChild(el.childNodes[0]);
        }
    };
}
