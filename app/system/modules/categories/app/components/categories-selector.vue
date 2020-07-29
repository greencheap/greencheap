<template>
    <ul class="uk-list">
        <li v-for="category in categories" :key="category.id">
            <label><input v-model="values" type="checkbox" class="uk-checkbox uk-margin-small-right" :value="category.id"> {{ category.title }}</label>
            <div v-if="category.has_subcategory" class="uk-margin-small-top">
                <v-categories v-model="values" :depth="category.id" :category-type="categoryType" />
            </div>
        </li>
    </ul>
</template>

<script>

export default {
    name: 'VCategories',

    props: {
        categoryType: {
            type: String,
            default: null,
        },
        depth: {
            type: Number,
            default: 0,
        },
        // eslint-disable-next-line vue/require-default-prop
        categoriesId: Array,
    },

    data() {
        return {
            categories: false,
            values: this.categoriesId,
        };
    },

    watch: {
        values: {
            handler() {
                this.updateCategory();
            },
            immediate: true,
        },
    },

    created() {
        this.load();
    },

    methods: {
        load() {
            this.$http.get('admin/api/categories/get-option', {
                params: {
                    type: this.categoryType,
                    depth: this.depth,
                },
            }).then((res) => {
                this.$set(this, 'categories', res.data.categories);
            });
        },

        updateCategory() {
            this.$emit('input', this.values);
        },

        updateSubCategory() {
         
        },
    },
};
</script>
