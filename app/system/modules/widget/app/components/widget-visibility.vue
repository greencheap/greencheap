<template>
    <div class="uk-form-horizontal">
        <div class="uk-margin">
            <label class="uk-form-label">{{ 'Pages' | trans }}</label>
            <div v-if="config.menus" class="uk-form-controls uk-form-controls-text">
                <input-tree v-model="widget.nodes" :active.sync="widget.nodes" />
            </div>
        </div>
    </div>
</template>

<script>

export default {

    section: {
        label: 'Visibility',
        priority: 100,
    },

    data() {
        return {
            menus: false,
        };
    },

    props: ['widget', 'config', 'form'],

};

</script>
