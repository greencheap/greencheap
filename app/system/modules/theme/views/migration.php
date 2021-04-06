<html lang="<?= str_replace("_", "-", $app["translator"]->getLocale()) ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $view->render("head") ?>
    <?php $view->script("system_theme", "system/theme:app/bundle/theme.js", ["uikit", "vue"]); ?>
</head>
<body>
    <section class="uk-section uk-section-default tm-greencheap-concept uk-background-image uk-background-cover" data-src="<?= $view->url()->getStatic("app/system/modules/theme/assets/images/default-bg.svg") ?>" uk-img>
        <div class="tm-greencheap-content-wrapper">
            <div class="uk-text-center">
                <img width="200" src="<?= $view->url()->getStatic("app/system/modules/theme/assets/images/greencheap-logo.svg") ?>" alt="GreenCheap">
                <div class="uk-panel uk-width-xlarge uk-margin">
                    <h1 class="uk-h3 uk-margin-small uk-text-bold uk-text-uppercase"><?= __("Migrate GreenCheap") ?></h1>
                    <p class="uk-margin uk-text-italic"><?= __("GreenCheap has been updated! Before we send you on your way, we have to update your database to the newest version.") ?></p>
                </div>
                <form class="uk-panel uk-panel-box" action="<?= $view->url("@system/migration/migrate") ?>">
                    <?php if ($redirect): ?>
                        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
                    <?php endif; ?>
                    <button class="uk-button uk-button-primary uk-width-medium" type="submit" value=""><?= __("Update") ?></button>
                    <?php $view->token()->get(); ?>
                </form>
            </div>
        </div>
    </section>
    <?= $view->render("footer") ?>
</body>
</html>
