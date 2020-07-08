<template>
    <div class="uk-margin">
        <label for="form-link-page" class="uk-form-label">{{ 'View' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-link-page" v-model="page" class="uk-width-1-1 uk-select">
                <option v-for="p in pages" :key="p.id" :value="p.id">
                    {{ p.title }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>

var LinkPage = {

    link: {
        label: 'Page',
    },

    props: ['link'],

    data() {
        return {
            pages: [],
            page: '',
        };
    },

    created() {
        // TODO don't retrieve entire page objects
        this.$http.get('api/site/page').then(function (res) {
            this.pages = res.data;
            if (this.pages.length) {
                this.page = this.pages[0].id;
            }
        });
    },

    watch: {

        page(page) {
            this.$parent.link = `@page/${page}`;
        },

    },

};

export default LinkPage;

window.Links.default.components['link-page'] = LinkPage;

</script>
