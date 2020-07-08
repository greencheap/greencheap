<?php if ($user->isAuthenticated()): ?>

<?= __('Hi %username%', ['%username%' => $user->username]) ?><br>
<a href="<?= $view->url('@user/logout', ['redirect' => $redirect]) ?>"><?= __('Logout') ?></a>

<?php else: ?>

<form action="<?= $view->url('@user/authenticate') ?>" method="post">

    <div class="uk-margin">
        <input class="uk-width-1-1 uk-input" type="text" name="credentials[username]" value="<?= $this->escape($last_username) ?>" placeholder="<?= __('username') ?>">
    </div>

    <div class="uk-margin">
        <input class="uk-width-1-1 uk-input" type="password" name="credentials[password]" value="" placeholder="<?= __('password') ?>">
    </div>

    <div class="uk-margin">
        <button class="uk-button uk-button-primary uk-width-1-1" type="submit"><?= __('Login') ?></button>
    </div>

    <p>
        <label><input type="checkbox" name="remember_me"> <?= __('Remember Me') ?></label>
        <br><a href="<?= $view->url('@user/resetpassword') ?>"><?= __('Forgot Password?') ?></a>
        <?php if ($app->module('system/user')->config('registration') != 'admin'): ?>
        <br><a href="<?= $view->url('@user/registration') ?>"><?= __('Sign up') ?></a>
        <?php endif ?>
    </p>

    <input type="hidden" name="redirect" value="<?= $this->escape($redirect) ?>">
    <?php $view->token()->get() ?>

</form>

<?php endif; ?>
