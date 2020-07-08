<template>
    <div class="uk-margin">
        <label for="form-link-user" class="uk-form-label">{{ 'View' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-link-user" v-model="url" class="uk-width-1-1 uk-select">
                <option value="@user/login">
                    {{ 'User Login' | trans }}
                </option>
                <option value="@user/logout">
                    {{ 'User Logout' | trans }}
                </option>
                <option value="@user/registration">
                    {{ 'User Registration' | trans }}
                </option>
                <option value="@user/profile">
                    {{ 'User Profile' | trans }}
                </option>
                <option value="@user/resetpassword">
                    {{ 'User Password Reset' | trans }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>

var LinkUser = {

    link: {
        label: 'User',
    },

    props: ['link'],

    data() {
        return {
            url: '',
        };
    },

    created() {
        this.url = '@user/login';
    },

    watch: {
        url(url) {
            this.$parent.link = url;
        },
    },


};

export default LinkUser;

window.Links.default.components['link-user'] = LinkUser;

</script>
