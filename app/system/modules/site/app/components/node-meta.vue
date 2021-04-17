<template>
    <div class="uk-form-horizontal">
        <div class="uk-margin">
            <label for="form-meta-title" class="uk-form-label">{{ 'Title' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-meta-title" v-model="node.data.meta['og:title']" class="uk-form-width-large uk-input" type="text">
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-meta-description" class="uk-form-label">{{ 'Description' | trans }}</label>
            <div class="uk-form-controls">
                <textarea id="form-meta-description" v-model="node.data.meta['og:description']" class="uk-form-width-large uk-textarea" rows="5" type="text" />
            </div>
        </div>

        <div class="uk-margin">
            <label for="form-meta-image" class="uk-form-label">{{ 'Image' | trans }}</label>
            <div class="uk-form-controls">
                <input-image
                    v-model="node.data.meta['og:image']"
                    :source.sync="node.data.meta['og:image']"
                    input-class="uk-form-width-large"
                />
            </div>
        </div>
    </div>
</template>

<script>

var Meta = {

    section: {
        label: 'Meta',
        priority: 100,
    },

    props: ['node'],

    created() {
        if (!this.node.data.meta) {
            this.$set(this.node.data, 'meta', { 'og:title': '' });
        }
    },

};

export default Meta;

window.Site.components['node-meta'] = Meta;

</script>
