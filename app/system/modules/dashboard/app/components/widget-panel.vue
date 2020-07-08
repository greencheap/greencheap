<template>
    <div>
        <div v-if="!type.disableToolbar" class="uk-card-badge">
            <ul class="uk-iconnav uk-invisible-hover">
                <li v-show="type.editable !== false && !editing">
                    <a uk-icon="file-edit" :title="'Edit' | trans" uk-tooltip="delay: 500" @click.prevent="edit" />
                </li>
                <li v-show="!editing">
                    <a uk-icon="more-vertical" class="uk-sortable-handle" :title="'Drag' | trans" uk-tooltip="delay: 500" />
                </li>
                <li v-show="editing">
                    <a v-confirm="'Delete widget?'" uk-icon="trash" :title="'Delete' | trans" uk-tooltip="delay: 500" @click.prevent="remove" />
                </li>
                <li v-show="editing">
                    <a uk-icon="check" :title="'Close' | trans" uk-tooltip="delay: 500" @click.prevent="save" />
                </li>
            </ul>
        </div>

        <component :is="type.component" :widget="widget" :editing="editing" />
    </div>
</template>

<script>

export default {

    name: 'panel',

    props: { widget: {} },

    data() {
        return {
            editing: false,
        };
    },

    created() {
        this.$options.components = this.$parent.$options.components;
    },


    computed: {

        type() {
            return this.$root.getType(this.widget.type);
        },

    },

    methods: {

        edit() {
            this.$set(this, 'editing', true);
        },

        save() {
            this.$root.save(this.widget);
            this.$set(this, 'editing', false);
        },

        remove() {
            this.$root.remove(this.widget);
        },

    },

};

</script>
