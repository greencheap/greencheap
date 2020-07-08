<?php $view->script('registration', 'system/user:app/bundle/registration.js', ['vue']) ?>

<validation-observer tag="form" id="user-registration" class="pk-user pk-user-registration uk-form-stacked uk-width-1-2@m uk-width-1-3@l uk-container" ref="observer" @submit.prevent="valid" v-cloak>

    <h1 class="uk-h2 uk-text-center"><?= __('Create an account') ?></h1>

    <div class="uk-alert-danger" uk-alert v-show="error">{{ error }}</div>

    <v-input view="type: icon, icon:user, class: uk-input uk-form-width-large, containerClass: uk-margin" type="text" name="username" placeholder="<?= __('Username') ?>" :rules="{required:true, regex:/^[a-zA-Z0-9._\-]{3,}$/}" v-model="user.username" message="Username is invalid."></v-input>

    <v-input view="type: icon, icon:user, class: uk-input uk-form-width-large, containerClass: uk-margin" type="text" name="name" placeholder="<?= __('Name') ?>" rules="required" v-model="user.name" message="Name cannot be blank."></v-input>

    <v-input view="type: icon, icon: mail, class: uk-input uk-form-width-large, containerClass: uk-margin" type="email" name="email" placeholder="<?= __('Email') ?>" rules="required|email" v-model="user.email" message="Email address is invalid."></v-input>

    <v-input :view="view" name="password" :type="hidePassword ? 'password' : 'text'" placeholder="<?= __('Password') ?>" v-model="user.password" :rules="{required: true, regex:/^.{6,}$/}" message="Password must be 6 characters or longer."></v-input>

    <div class="uk-margin">
        <button class="uk-button uk-button-primary uk-button-large uk-width-1-1" type="submit"><?= __('Sign up') ?></button>
    </div>

    <p class="uk-text-center"><?= __('Already have an account?') ?> <a href="<?= $view->url('@user/login') ?>"><?= __('Login!') ?></a></p>

</validation-observer>
