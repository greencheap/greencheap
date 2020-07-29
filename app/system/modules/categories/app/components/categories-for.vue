<template>
    <div>
        <ul class="uk-list uk-list-divider uk-margin-small-top">
            <li v-for="(category , id) in categories" :key="id">
                <label><input :value="category.id" type="checkbox" class="uk-checkbox uk-margin-small-right">{{ category.title }}</label>
                <categoriesfor v-if="category.subcategories" class="uk-padding-small-left" :categories.sync="category.subcategories" />
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'Categoriesfor',

    // eslint-disable-next-line vue/require-prop-types
    props: ['categories'],
};
</script>
