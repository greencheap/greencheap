import { ValidationObserver, VInput } from "SystemApp/components/validation.vue";

var ResetConfirm = {
    el: "#reset-confirm",

    data() {
        return {
            error: null,
            hidePassword: true,
            view: {
                type: "icon",
                containerClass: "uk-margin",
                class: "uk-input uk-form-width-large",
                icon: () => (this.hidePassword ? "lock" : "unlock"),
                iconClick: () => {
                    this.hidePassword = !this.hidePassword;
                },
                iconTag: "a",
                iconDir: "right",
            },
        };
    },

    methods: {
        async valid() {
            const isValid = await this.$refs.resetform.validate();
            if (isValid) {
                this.$el.submit();
            }
        },
    },

    components: {
        ValidationObserver,
        VInput,
    },
};

export default ResetConfirm;

Vue.ready(ResetConfirm);
