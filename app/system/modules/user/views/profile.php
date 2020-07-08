<?php $view->script('profile', 'system/user:app/bundle/profile.js', ['vue']) ?>

<validation-observer tag="form" id="user-profile" class="uk-container uk-width-large" ref="observer" @submit.prevent="submit" v-cloak>

    <h1 class="uk-h2 uk-text-center">{{ 'Your Profile' | trans }}</h1>

    <v-input type="text" name="name" view="class: uk-input uk-form-width-large, containerClass: uk-margin" placeholder="<?= __('Name') ?>" v-model="user.name" rules="required" message="Name cannot be blank."></v-input>

    <v-input type="email" name="email" view="class: uk-input uk-form-width-large, containerClass: uk-margin" placeholder="<?= __('Email') ?>" v-model="user.email" rules="required|email" message="Invalid Email."></v-input>

    <div class="uk-margin">
        <a href="#" @click.prevent="changePassword = !changePassword" class="uk-text-small">{{ 'Change password' | trans }}</a>
    </div>

    <div v-if="changePassword" class="uk-margin">

        <v-input :type="hidePassword ? 'password' : 'text'"
            name="password_old"
            :view="view"
            placeholder="<?= __('Current Password') ?>"
            v-model="user.password.old"
            rules="required"
            message="Password cannot be blank.">
        </v-input>

        <v-input :type="hidePassword ? 'password' : 'text'"
            name="password_new"
            :view="view"
            placeholder="<?= __('New Password') ?>"
            v-model="user.password.new"
            rules="required"
            message="Password cannot be blank.">
        </v-input>

    </div>

    <div class="uk-margin">
        <button class="uk-button uk-button-primary uk-button-large uk-width-1-1" type="submit">{{ 'Save' | trans }}</button>
    </div>

</validation-observer>
