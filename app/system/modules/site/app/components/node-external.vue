<template>
    <div class="uk-form-horizontal">
        <div class="uk-margin">
            <label for="form-url" class="uk-form-label">{{ "Url" | trans }}</label>
            <div class="uk-form-controls">
                <input v-model="node.link" type="url" class="uk-input uk-form-width-large" required="Invalid url." />
            </div>
        </div>

        <component :is="'template-settings'" :node.sync="node" :roles.sync="roles" />
    </div>
</template>

<script>
export default {
    section: {
        label: "Settings",
        priority: 0,
        active: "external",
    },

    props: ["node", "roles", "form"],

    inject: ["$components"],

    created() {
        _.extend(this.$options.components, this.$components);

        if (!this.node.id) {
            this.node.status = 1;
        }
    },

    events: {
        // eslint-disable-next-line func-names
        "save:node": function () {},
    },
};
</script>
