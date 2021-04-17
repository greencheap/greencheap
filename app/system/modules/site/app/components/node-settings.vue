<template>
    <div class="uk-form-horizontal">
        <component :is="'template-settings'" :node="node" :roles="roles" />
    </div>
</template>

<script>

export default {

    props: ['node', 'roles'],

    section: {
        label: 'Settings',
    },

    inject: ['$components'],

    created() {
        _.extend(this.$options.components, this.$components);
    },

};

</script>
