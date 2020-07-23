<template>
    <div class="uk-grid-small" uk-grid>
        <div class="uk-width-expand@m">
            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Title' | trans }}</label>
                <div class="uk-form-controls">
                    <input v-model="category.title" type="text" class="uk-input uk-width-expand" required>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Content' | trans }}</label>
                <div class="uk-form-controls">
                    <v-editor v-model="category.data.content" :options="{markdown : true , height: 300}" />
                </div>
            </div>
        </div>
        <div class="uk-width-medium@m">
            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Status' | trans }}</label>
                <div class="uk-form-controls">
                    <select v-model="category.status" class="uk-select uk-width-expand">
                        <option v-for="(status , id) in data.statuses" :key="id" :value="id">
                            {{ status }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Slug' | trans }}</label>
                <div class="uk-form-controls">
                    <input v-model="category.slug" type="text" class="uk-input uk-width-expand">
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Sub Category' | trans }}</label>
                <div class="uk-form-controls">
                    <select v-model="category.sub_category" class="uk-select uk-width-expand">
                        <option value="0">
                            {{ 'No Sub Category' | trans }}
                        </option>
                        <option v-for="sub_category in data.categories" :key="sub_category.id" :value="sub_category.id">
                            {{ sub_category.title }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Type' | trans }}</label>
                <div class="uk-form-controls">
                    <input v-model="category.type" type="text" class="uk-input uk-width-expand" disabled>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Date' | trans }}</label>
                <div class="uk-form-controls">
                    <input-date v-model="category.date" />
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'User' | trans }}</label>
                <div class="uk-form-controls">
                    <select v-model="category.user_id" class="uk-select uk-width-expand">
                        <option v-for="user in data.users" :key="user.id" :value="user.id">
                            {{ user.name }} - {{ user.username }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    // eslint-disable-next-line vue/require-prop-types
    props: ['data', 'category'],
    section: {
        label: 'Section',
        priority: 10,
    },
};
</script>
