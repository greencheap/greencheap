<template>
    <div class="uk-form-horizontal">
        <div class="uk-margin">
            <label for="form-url" class="uk-form-label">{{ 'Url' | trans }}</label>
            <div class="uk-form-controls">
                <input-link id="form-url" name="link" v-model="node.link" input-class="uk-form-width-large" required="Invalid url."></input-link>
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-type" class="uk-form-label">{{ 'Type' | trans }}</label>

            <div class="uk-form-controls">
                <select id="form-type" v-model="behavior" class="uk-form-width-large uk-select">
                    <option value="link">
                        {{ 'Link' | trans }}
                    </option>
                    <option value="alias">
                        {{ 'URL Alias' | trans }}
                    </option>
                    <option value="redirect">
                        {{ 'Redirect' | trans }}
                    </option>
                </select>
            </div>
        </div>

        <component :is="'template-settings'" :node.sync="node" :roles.sync="roles" />
    </div>
</template>

<script>

export default {

    section: {
        label: 'Settings',
        priority: 0,
        active: 'link',
    },

    props: ['node', 'roles', 'form'],

    inject: ['$components'],

    computed: {

        behavior: {

            get() {
                if (this.node.data.alias) {
                    return 'alias';
                } if (this.node.data.redirect) {
                    return 'redirect';
                }

                return 'link';
            },

            set(type) {
                this.$set(this.node, 'data', _.extend(this.node.data, {
                    alias: type === 'alias',
                    redirect: type === 'redirect' ? this.node.link : false,
                }));
            },

        },

    },

    created() {
        _.extend(this.$options.components, this.$components);

        if (this.behavior === 'redirect') {
            this.node.link = this.node.data.redirect;
        }

        if (!this.node.id) {
            this.node.status = 1;
        }
    },

    events: {

        'save:node': function () {
            if (this.behavior === 'redirect') {
                this.node.data.redirect = this.node.link;
            }
        },

    },

};

</script>
