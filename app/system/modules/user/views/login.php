<form class="pk-user pk-user-login uk-form-stacked uk-width-1-2@m uk-width-1-3@l uk-container" action="<?= $view->url('@user/authenticate') ?>" method="post">

    <h1 class="uk-h2 uk-text-center"><?= __('Sign in to your account') ?></h1>

    <?= $view->render('messages') ?>

    <div class="uk-margin">
        <div class="uk-inline uk-width-1-1">
            <span class="uk-form-icon" uk-icon="icon: user"></span>
            <input class="uk-input" type="text" name="credentials[username]" value="<?= $this->escape($last_username) ?>" placeholder="<?= __('Username') ?>" required autofocus>
        </div>
    </div>

    <div class="uk-margin">
        <div class="uk-inline uk-width-1-1">
            <span class="uk-form-icon" uk-icon="icon: lock"></span>
            <input class="uk-input" type="password" name="credentials[password]" value="" placeholder="<?= __('Password') ?>" required>
        </div>
    </div>

    <p class="uk-margin uk-margin-small-bottom">
        <button class="uk-button uk-button-primary uk-button-large uk-width-1-1" type="submit"><?= __('Sign in') ?></button>
    </p>

    <ul class="uk-list uk-flex uk-flex-between uk-flex-middle uk-text-small uk-margin-small-top uk-margin-remove-bottom">
        <li>
            <label><input class="uk-checkbox" type="checkbox" name="remember_me"> <?= __('Remember Me') ?></label>
        </li>
        <li class="uk-text-right uk-margin-remove">
            <a class="uk-link" href="<?= $view->url('@user/resetpassword') ?>"><?= __('Request Password') ?></a>
        </li>
    </ul>

    <?php if ($app->module('system/user')->config('registration') != 'admin') : ?>
    <p class="uk-text-center"><?= __('No account yet?') ?> <a href="<?= $view->url('@user/registration') ?>"><?= __('Sign up now') ?></a></p>
    <?php endif ?>

    <input type="hidden" name="redirect" value="<?= $this->escape($redirect) ?>">
    <?php $view->token()->get() ?>

</form>
