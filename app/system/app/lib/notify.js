export default function (Vue) {
    Vue.prototype.$notify = function () {
        const args = arguments;
        const msgs = document.getElementsByClassName("pk-system-messages");
        const UIkit = window.UIkit || {};
        const message = args[0] ? this.$trans(args[0]) : "Unrecognized error.";
        const status = args[1] ? args[1] : "primary";

        if (UIkit.notification) {
            let icons;
            if (status == "warning") {
                icons = '<i uk-icon="icon:info;ratio:1.3" class="uk-margin-right"></i>';
            } else if (status == "danger") {
                icons = '<i uk-icon="icon:close;ratio:1.3" class="uk-margin-right"></i>';
            } else {
                icons = '<i uk-icon="icon:check;ratio:1.3" class="uk-margin-right"></i>';
            }
            UIkit.notification({
                message: `${icons}${message}`,
                status,
                pos: "top-center",
            });
        } else if (msgs) {
            msgs.empty().append(`<div uk-alert><p class="uk-alert-${status}">${message}</p></div>`);
        }
    };
}
