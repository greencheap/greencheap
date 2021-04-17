<template>
    <div class="pk-grid-large pk-width-sidebar-large uk-form-stacked" uk-grid>
        <div class="pk-width-content">
            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Title' | trans }}</label>
                <v-input name="page[title]" type="text" rules="required" view="class: uk-input uk-form-large" v-model.lazy="page.title" placeholder="Enter Title" message="Title cannot be blank." />
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Content' | trans }}</label>
                <v-editor v-model="page.content" :options="{markdown : page.data.markdown}" />
                <div class="uk-margin-small-top">
                    <label>
                        <input v-model="page.data.markdown" class="uk-checkbox" type="checkbox">
                        <span class="uk-margin-small-left">{{ 'Enable Markdown' | trans }}</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="pk-width-sidebar">
            <div class="uk-panel">
                <div class="uk-margin">
                    <label for="form-menu-title" class="uk-form-label">{{ 'Menu Title' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-menu-title" v-model="node.title" class="uk-form-width-large uk-input" type="text" name="title">
                    </div>
                </div>

                <div class="uk-margin">
                    <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-slug" v-model="node.slug" class="uk-form-width-large uk-input" type="text">
                    </div>
                </div>

                <div class="uk-margin">
                    <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-status" v-model="node.status" class="uk-form-width-large uk-select">
                            <option value="0">
                                {{ 'Disabled' | trans }}
                            </option>
                            <option value="1">
                                {{ 'Enabled' | trans }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label">{{ 'Restrict Access' | trans }}</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <div v-for="role in roles" :key="role.id" class="uk-margin-small">
                            <label>
                                <input v-model.number="node.roles" class="uk-checkbox" type="checkbox" :value="role.id">
                                <span class="uk-margin-small-left">{{ role.name }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label">{{ 'Menu' | trans }}</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label>
                            <input v-model="node.data.menu_hide" class="uk-checkbox" type="checkbox" value="center-content">
                            <span class="uk-margin-small-left">{{ 'Hide in menu' | trans }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

var PageSettings = {

    section: {
        label: 'Content',
    },

    inject: ['$components'],

    props: ['node', 'roles', 'form'],

    data() {
        return {
            page: {
                data: { title: true },
            },
        };
    },

    created(){
        _.extend(this.$options.components, this.$components);
    },

    mounted() {
        if (!this.node.id) this.node.status = 1;
    },

    events: {

        'save:node': function (event, data) {
            data.page = this.page;

            if (!this.node.title) {
                this.node.title = this.page.title;
            }
        },

    },

    watch: {

        'node.data.defaults.id': {

            handler(id) {
                if (id) {
                    this.$http.get('api/site/page{/id}', { params: { id } }).then(function (res) {
                        this.$set(this, 'page', res.data);
                    });
                }
            },

            immediate: true,

        },

    },

};

export default PageSettings;

window.Site.components['page.settings'] = PageSettings;

</script>
