<html lang="<?= str_replace('_', '-', $app['translator']->getLocale()) ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= $view->render('head') ?>
        <?php $view->script('system_theme','system/theme:app/bundle/theme.js' , ['uikit' , 'vue']) ?>
    </head>
    <body>
        <section class="uk-section uk-section-default tm-greencheap-concept uk-background-image uk-background-cover" data-src="<?= $view->url()->getStatic('app/system/modules/theme/assets/images/default-bg.svg') ?>" uk-img>
            <div class="tm-greencheap-content-wrapper">
                <div class="uk-text-center">
                    <h1 class="uk-heading-2xlarge uk-text-bold uk-text-secondary"><?= $exception->getCode() ?></h1>
                    <div class="uk-panel uk-width-xlarge uk-margin">
                        <h2 class="uk-h3 uk-margin-small uk-text-bold uk-text-uppercase"><?= $title ?></h2>
                        <p class="uk-margin uk-text-italic"><?= $message ?></p>
                    </div>
                </div>
            </div>
        </section>
        <?= $view->render('footer') ?>
    </body>
</html>
