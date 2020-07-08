<?php $view->script('reset-confirm', 'system/user:app/bundle/reset-confirm.js', ['vue']) ?>

<validation-observer tag="form" id="reset-confirm" class="pk-user pk-user-reset uk-form-stacked uk-width-1-2@m uk-width-1-3@l uk-container" ref="resetform" action="<?= $view->url('@user/resetpassword/confirm', ['key' => $activation]) ?>" method="post" v-cloak>

    <?php if($error): ?>
    <div class="uk-alert-danger" uk-alert>
        <?= $error; ?>
    </div>
    <?php endif; ?>

    <h1 class="uk-h2 uk-text-center"><?= __('Password Confirmation') ?></h1>

    <v-input :view="view" :type="hidePassword ? 'password' : 'text'" placeholder="<?= __('New Password') ?>" name="password" :rules="{required:true, regex:/^.{6,}$/}" message="Password cannot be blank and must be 6 characters or longer."></v-input>

    <div class="uk-margin">
        <button class="uk-button uk-button-primary uk-button-large uk-width-1-1" @click.prevent="submit"><?= __('Confirm') ?></button>
    </div>

    <?php $view->token()->get() ?>

</validation-observer>
