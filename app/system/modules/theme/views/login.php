<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= $view->url()->getStatic('system/theme:favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
    <link href="<?= $view->url()->getStatic('system/theme:apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
    <?= $view->render('head') ?>
    <?php $view->script('system_theme', 'system/theme:app/bundle/theme.js', ['uikit']) ?>
</head>
<body>
    <section id="app" class="uk-grid-collapse" uk-grid>
        <div class="uk-width-2-3@m uk-width-1-2@s uk-visible@s uk-background-primary">
            <div class="uk-position-relative uk-visible-toggle" tabindex="-1" uk-slideshow="ratio: false;autoplay:true">
                <ul class="uk-slideshow-items" uk-height-viewport>
                    <?php foreach($images as $image): ?>
                        <li>
                            <img data-src="<?= $image['image'] ?>" alt="<?= $image['information']['label'] ?>" uk-img uk-cover>
                            <div class="uk-position-bottom-left uk-position-small uk-text-small">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="uk-margin-small-right">
                                        <a href="<?= $image['author']['profileUrl'] ?>" target="_blank">
                                            <img data-src="<?= $image['author']['avatarUrl'] ?>" width="40" class="uk-border-circle" uk-img>
                                        </a>
                                    </div>
                                    <div class="uk-light">
                                        <a href="<?= $image['author']['profileUrl'] ?>" target="_blank">
                                            <h4 class="uk-h6 uk-margin-remove uk-text-bold"><?= $image['author']['fullName'] ?></h4>
                                        </a>
                                        <span class="uk-display-block"><i uk-icon="icon:flag-alt;ratio:0.8"></i> <?= $image['information']['locationName'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
                <a class="uk-position-center-left uk-position-small uk-hidden-hover uk-light" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                <a class="uk-position-center-right uk-position-small uk-hidden-hover uk-light" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
                </div>
            </div>
        </div>
        <div class="uk-width-1-3@m uk-width-1-2@s uk-flex uk-flex-center uk-flex-middle" uk-height-viewport="expand:true">
            <div class="uk-width-expand uk-padding uk-text-center">
                <img data-src="<?= $view->url()->getStatic('app/system/modules/theme/assets/images/greencheap-logo.svg') ?>" width="150" uk-img>
                <?= $view->render('messages') ?>
                <form class="js-login js-toggle" action="<?= $view->url('@user/authenticate') ?>" method="post">
                    <h1 class="uk-text-center uk-h3"><?= __('Sign In') ?></h1>
                    <p><?= __('A panel where you can manage and navigate your website.') ?></p>
                    <div class="uk-panel">
                        <div class="uk-margin uk-inline uk-width-expand">
                            <span class="uk-form-icon" uk-icon="icon:user"></span>
                            <input class="uk-width-expand uk-input uk-form-large" type="text" name="credentials[username]" value="<?= $this->escape($last_username) ?>" placeholder="<?= __('Username') ?>" autofocus>
                        </div>
                        <div class="uk-margin uk-inline uk-width-expand">
                            <span class="uk-form-icon" uk-icon="icon:key"></span>
                            <input class="uk-width-expand uk-input uk-form-large" type="password" name="credentials[password]" value="" placeholder="<?= __('Password') ?>">
                        </div>
                        <p class="uk-margin">
                            <button class="uk-button uk-button-primary uk-button-large uk-width-expand"><?= __('Login') ?></button>
                        </p>
                        <?php $view->token()->get() ?>
                        <input type="hidden" name="redirect" value="<?= $this->escape($redirect) ?>">
                    </div>
                    <ul class="uk-list">
                        <li><label class="uk-form"><input class="uk-checkbox" type="checkbox" name="remember_me"> <?= __('Remember Me') ?></label></li>
                        <li class="uk-margin-small-top"> <?= __('Forgot Password?') ?> <a class="uk-link" uk-toggle="target: .js-toggle"><?= __('Request Password') ?></a></li>
                    </ul>
                </form>
                <form class="js-toggle" action="<?= $view->url('@user/resetpassword/request') ?>" method="post" hidden>
                    <h1 class="uk-text-center uk-h3"><?= __('Reset Password') ?></h1>
                    <p><?= __('If you have forgotten your password, you can request to change your user password here.') ?></p>
                    <div class="uk-panel">
                        <div class="uk-margin uk-inline uk-width-expand">
                            <span class="uk-form-icon" uk-icon="icon:mail"></span>
                            <input class="uk-width-expand uk-input uk-form-large" type="text" name="email" value="" placeholder="<?= __('Email') ?>" required>
                        </div>
                        <p class="uk-margin">
                            <button class="uk-button uk-button-secondary uk-button-large uk-width-expand"><?= __('Reset Password') ?></button>
                        </p>
                        <?php $view->token()->get() ?>
                        <ul class="uk-list">
                            <li class="uk-margin-small-top"> <?= __('Back to the login page') ?> <a class="uk-link" uk-toggle="target: .js-toggle"><?= __('Sign In') ?></a></li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
