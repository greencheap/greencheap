<template>
    <button :type="type" :class="classContainer" :disabled="disabled || loader">
        <span v-if="loader" class="uk-margin-small-right" uk-spinner></span>
        {{ text }}
    </button>
</template>
<script>
export default {
    props: {
        text: {
            type: String,
            default: "Button",
        },
        isLoader: {
            type: Boolean,
            default: false,
        },
        view: {
            type: [Object, String],
            default: () => ({}),
        },
        type: {
            type: String,
            default: "button",
        },
        isDisabled: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            classContainer: "",
            loader: null,
        };
    },

    created() {
        if (typeof this.view === "string") {
            this.classContainer = this.view;
        } else {
            this.classContainer = this.view.class;
        }

        this.loader = this.isLoader;
        this.disabled = this.isDisabled;
    },

    methods: {
        disabledOnButton(val = true) {
            this.disabled = val;
        },

        loaderOnButton(val = true) {
            this.loader = val;
        },
    },
};
</script>
