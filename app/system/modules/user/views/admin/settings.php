<?php $view->script('settings', 'system/user:app/bundle/settings.js', ['vue', 'input-link']) ?>

<div id="settings" class="uk-form-horizontal" v-cloak>

    <div class="uk-margin uk-flex uk-flex-middle uk-flex-between uk-flex-wrap" >
        <div>
            <h2 class="uk-h3 uk-margin-remove">{{ 'Settings' | trans }}</h2>
        </div>
        <div class="uk-margin">
            <button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label">{{ 'Registration' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-margin-small">
                <label><input class="uk-radio" type="radio" v-model="config.registration" value="admin"><span class="uk-margin-small-left">{{ 'Disabled' | trans }}</span></label>
            </p>
            <p class="uk-margin-small">
                <label><input class="uk-radio" type="radio" v-model="config.registration" value="guest"><span class="uk-margin-small-left">{{ 'Enabled' | trans }}</span></label>
            </p>
            <p class="uk-margin-small">
                <label><input class="uk-radio" type="radio" v-model="config.registration" value="approval"><span class="uk-margin-small-left">{{ 'Enabled, but approval is required.' | trans }}</span></label>
            </p>
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label">{{ 'Users per page' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-margin-small">
                <input type="number" v-model="config.users_per_page" class="uk-form-width-small uk-input">
            </p>
        </div>
    </div>

    <div class="uk-margin">
        <label for="form-user-verification" class="uk-form-label">{{ 'Verification' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <label><input id="form-user-verification" class="uk-checkbox" type="checkbox" v-model="config.require_verification"><span class="uk-margin-small-left">{{ 'Require e-mail verification when a guest creates an account.' | trans }}</span></label>
        </div>
    </div>

    <div class="uk-margin">
        <label for="form-redirect" class="uk-form-label">{{ 'Login Redirect' | trans }}</label>
        <div class="uk-form-controls">
           <input-link id="form-redirect" input-class="uk-form-width-large" v-model="config.login_redirect"></input-link>
        </div>
    </div>

</div>
