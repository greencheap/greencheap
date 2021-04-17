<template>
    <div>
        <div class="uk-margin">
            <label for="form-style" class="uk-form-label">{{ 'Extension' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-style" v-model="type" class="uk-width-1-1 uk-select">
                    <option v-for="(type, key) in types" :key="key" :value="type.value">
                        {{ type.text | trans }}
                    </option>
                </select>
            </div>
        </div>

        <component :is="type" v-if="type" :link.sync="link" />
    </div>
</template>

<script>

export default {

    data() {
        return {
            type: false,
            link: '',
        };
    },

    watch: {

        type: {
            handler(type) {
                if (!type && this.types.length) {
                    this.type = this.types[0].value;
                }
            },
            immediate: true,
        }

    },

    computed: {

        types() {
            const types = []; let
                options;

            _.forIn(this.$options.components, (component, name) => {
                if (component.link) {
                    types.push({ text: component.link.label, value: name });
                }
            });

            return _.sortBy(types, 'text');
        },

    },

    components: {},

};

Vue.component('panel-link', (resolve) => {
    resolve(require('./panel-link.vue'));
});

</script>
