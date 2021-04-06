var Widgets = {
    data() {
        return {
            widgets: [],
        };
    },

    created() {
        this.resource = this.$resource("api/site/widget{/id}");
    },

    components: {
        /*
         * Moved to widget/app/views/edit.js
         */
    },
};

export default Widgets;

window.Widgets = Widgets;
