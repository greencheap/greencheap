<template>
    <div class="pk-grid-large pk-width-sidebar-large" uk-grid>
        <div class="pk-width-content uk-form-horizontal">
            <div class="uk-margin">
                <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
                <v-input id="form-title" type="text" name="title" placeholder="Enter Title" view="class: uk-form-width-large uk-input" rules="required" v-model="widget.title" message="Title cannot be blank."/>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Login Redirect' | trans }}</label>
                <div class="uk-form-controls wp-form-width-max-large">
                    <input-link v-model="widget.data.redirect_login" input-class="uk-form-width-large"></input-link>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Logout Redirect' | trans }}</label>
                <div class="uk-form-controls wp-form-width-max-large">
                    <input-link v-model="widget.data.redirect_logout" input-class="uk-form-width-large"></input-link>
                </div>
            </div>
        </div>
        <div class="pk-width-sidebar">
            <component :is="'template-settings'" :widget.sync="widget" :config.sync="config" :form="form" />
        </div>
    </div>
</template>

<script>

var WidgetLogin =  {

    section: {
        label: 'Settings',
    },

    replace: false,

    props: ['widget', 'config', 'form'],

    inject: ['$components'],

    created() {
        _.extend(this.$options.components, this.$components);
    },

};

export default WidgetLogin;

window.Widgets.components['system-login.settings'] = WidgetLogin;

</script>
