<template>
    <div class="uk-grid pk-grid-large pk-width-sidebar-large">
        <div class="pk-width-content uk-form-horizontal">
            <div class="uk-margin">
                <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
                <div class="uk-form-controls">
                    <v-input id="form-title" type="text" name="title" placeholder="Enter Title" view="class: uk-input uk-form-width-large" rules="required" v-model="widget.title" message="Title cannot be blank."/>
                </div>
            </div>
        </div>
        <div class="pk-width-sidebar">
            <component :is="'template-settings'" :widget.sync="widget" :config.sync="config" :form="form" />
        </div>
    </div>
</template>

<script>

export default {

    section: {
        label: 'Settings',
    },

    inject: ['$components'],

    props: ['widget', 'config'],

    created() {
        _.extend(this.$options.components, this.$components)
    },

};

</script>
