<template>
    <div class="pk-grid-large pk-width-sidebar-large" uk-grid>
        <div class="pk-width-content uk-form-stacked">
            <div class="uk-margin">
                <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
                <v-input id="form-title" type="text" name="title" placeholder="Enter Title" view="class: uk-width-1-1 uk-form-large uk-input" rules="required" v-model="widget.title" message="Title cannot be blank."/>
            </div>

            <div class="uk-margin">
                <label for="form-title" class="uk-form-label">{{ 'Content' | trans }}</label>
                <v-editor v-model="widget.data.content" :options="{markdown : widget.data.markdown}" />
                <p class="uk-margin-small-top">
                    <label><input v-model="widget.data.markdown" class="uk-checkbox" type="checkbox"><span class="uk-margin-small-left">{{ 'Enable Markdown' | trans }}</span></label>
                </p>
            </div>
        </div>
        <div class="pk-width-sidebar">
            <component :is="'template-settings'" :widget.sync="widget" :config.sync="config" :form="form" />
        </div>
    </div>
</template>

<script>

var WidgetText = {

    name: 'widget-text',

    section: {
        label: 'Settings',
    },

    inject: ['$components'],

    props: ['widget', 'config', 'form'],

    created() {
        _.extend(this.$options.components, this.$components);
    },

};

export default WidgetText;

window.Widgets.components['system-text.settings'] = WidgetText;

</script>
