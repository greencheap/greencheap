<template>
    <div uk-grid class="uk-grid-small">
        <div class="uk-width-2-3@s">
            <div class="uk-margin">
                <label for="form-username" class="uk-form-label">{{ 'Username' | trans }}</label>
                <v-input id="form-username" name="username" type="text"
                    view="class: uk-form-width-large uk-input"
                    v-model="user.username"
                    :rules="{required: true, regex: /^[a-zA-Z0-9._\-]+$/}"
                    autocomplete="new-username"
                    message='Username cannot be blank and may only contain alphanumeric characters (A-Z, 0-9) and some special characters ("._-")'
                />
            </div>

            <div class="uk-margin">
                <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>
                <v-input id="form-name" name="name" type="text"
                    view="class: uk-form-width-large uk-input"
                    v-model="user.name"
                    :rules="{required: true}"
                    autocomplete="new-name"
                    message='Name cannot be blank.'
                />
            </div>

            <div class="uk-margin">
                <label for="form-email" class="uk-form-label">{{ 'Email' | trans }}</label>
                <v-input id="form-email" name="email" type="email"
                    view="class: uk-form-width-large uk-input"
                    v-model.lazy="user.email"
                    :rules="{required: true, email: true}"
                    autocomplete="new-email"
                    message="Field must be a valid email address."
                />
            </div>

            <div class="uk-margin">
                <label for="form-password" class="uk-form-label">{{ 'Password' | trans }}</label>
                <div v-show="user.id && !editingPassword" class="uk-form-controls uk-form-controls-text">
                    <a href="#" @click.prevent="editingPassword = true" class="uk-text-small">{{ 'Change password' | trans }}</a>
                </div>
                <div class="uk-form-controls" :class="{'uk-hidden' : (user.id && !editingPassword)}">
                    <div class="uk-form-password">
                        <div class="uk-margin">
                            <div class="uk-inline">
                                <a @click.prevent="hidePassword = !hidePassword" :uk-tooltip="hidePassword ? 'Show' : 'Hide' | trans" delay="500" pos="right" class="uk-form-icon uk-form-icon-flip" href="#" :uk-icon="hidePassword ? 'lock': 'unlock'"></a>
                                <input id="form-password" v-model="password" autocomplete="new-password" class="uk-form-width-large uk-input" :type="hidePassword ? 'password' : 'text'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Status' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text">
                    <p v-for="(status, key) in config.statuses" :key="key" class="uk-margin-small">
                        <label><input v-model="user.status" class="uk-radio" type="radio" :value="parseInt(key)" :disabled="config.currentUser == user.id"><span class="uk-margin-small-left">{{ status }}</span></label>
                    </p>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Roles' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text">
                    <p v-for="role in config.roles" :key="role.id" class="uk-margin-small">
                        <label><input v-model="user.roles" class="uk-checkbox" type="checkbox" :value="role.id" :disabled="role.disabled"><span class="uk-margin-small-left">{{ role.name }}</span></label>
                    </p>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Last login' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text">
                    <p>{{ $trans('%date%', { date: user.login ? $date(user.login) : $trans('Never') }) }}</p>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">{{ 'Registered since' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text">
                    {{ user.registered ? $trans('%date%', { date: $date(user.registered) }) : '' }}
                </div>
            </div>
        </div>

        <div class="uk-width-expand@s">
            <div uk-grid class="uk-grid-collapse">
                <div class="uk-width-expand@s" />
                <div class="uk-width-5-6@s">
                    <div v-show="user.name" class="uk-card uk-card-default uk-text-center">
                        <div class="uk-card-media-top uk-padding-small">
                            <img v-gravatar="user.email" height="280" width="280" :alt="user.name" class="uk-width-1-1">
                        </div>

                        <div class="uk-card-footer">
                            <h3 class="uk-card-title uk-margin-remove-bottom uk-text-break">
                                {{ user.name }}
                                <i
                                    :title="(isNew ? 'New' : config.statuses[user.status]) | trans"
                                    :class="{
                                        'pk-icon-circle-primary': isNew,
                                        'pk-icon-circle-success': user.access && user.status,
                                        'pk-icon-circle-danger': !user.status
                                    }"
                                />
                            </h3>

                            <div>
                                <a class="uk-text-break uk-text-small" :href="'mailto:'+user.email">{{ user.email }}</a><i v-show="config.emailVerification && user.data.verified" uk-icon="icon: check" :title="'Verified email address' | trans" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {

    section: {
        label: 'User',
    },

    props: ['user', 'config', 'form'],

    inject: ['$components'],

    data() {
        return { password: '', hidePassword: true, editingPassword: false };
    },

    created() {
        _.extend(this.$options.components, this.$components);
    },

    mounted() {},

    computed: {

        isNew() {
            return !this.user.login && this.user.status;
        },

    },

    methods:{
        onChangeImage(event){
            const file = event.target.files[0];
            const formData = new FormData();
            
            if( file.size > 1000000 ){
                this.$notify('Image size too large can be up to 1MB.' , 'danger');
                return false;
            }

            if( file.type !== "image/jpg" && file.type !== "image/png" && file.type !== "image/jpeg" ){
                this.$notify('Only JPG and PNG extensions are accepted.' , 'danger');
                return false;
            }

            formData.append('_avatar' , file , file.name);
            this.$http.post('api/user/avatar-upload' , formData).then((res)=>{
                const path = res.body.path;
                this.user.data.avatar = path;
            })

        },

        deleteAvatar(){
            this.user.data.avatar = null
        }
    }, 

    events: {

        'save:user': function (e, data) {
            data.password = this.password;
        },

    },

};

</script>
